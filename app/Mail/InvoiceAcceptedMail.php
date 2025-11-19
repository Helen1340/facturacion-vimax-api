<?php

namespace App\Mail;

use App\Models\ElectronicInvoice;
use App\Models\ElectronicDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceAcceptedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $doc;

    public function __construct(ElectronicInvoice $invoice, ?ElectronicDocument $doc = null)
    {
        $this->invoice = $invoice->loadMissing(['buyer', 'user.company', 'invoiceDetails.item.taxes', 'invoiceDetails.item.measurementUnit']);
        $this->doc = $doc;
    }

    public function build()
    {
        $companyName = $this->invoice->user->company->business_name ?? 'Facturación';
        $subject = 'Factura aceptada por DIAN ' . $this->invoice->invoice_number;

        $mail = $this->subject($subject)
            ->view('emails.invoice_accepted', [
                'invoice' => $this->invoice,
                'doc' => $this->doc,
                'companyName' => $companyName,
            ]);

        if ($this->doc && $this->doc->xml_document) {
            $mail->attachData(
                $this->doc->xml_document,
                'invoice-' . $this->invoice->invoice_number . '.xml',
                ['mime' => 'application/xml']
            );
        }

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $this->invoice,
            'doc' => $this->doc
        ])->setPaper('letter');

        $mail->attachData(
            $pdf->output(),
            'invoice-' . $this->invoice->invoice_number . '.pdf',
            ['mime' => 'application/pdf']
        );

        return $mail;
    }
}