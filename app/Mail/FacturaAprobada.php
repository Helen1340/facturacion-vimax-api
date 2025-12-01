<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class FacturaAprobada extends Mailable
{
    use Queueable, SerializesModels;

    public $factura;
    public $cliente;
    public $pdfContent;

    public function __construct($factura, $cliente)
    {
        $this->factura = $factura;
        $this->cliente = $cliente;
        $this->pdfContent = $this->generatePDF($factura);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Factura #' . $this->factura->invoice_number . ' - Aprobada por la DIAN',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.factura-aprobada',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn() => $this->pdfContent, 'factura-' . $this->factura->invoice_number . '.pdf')
                ->withMime('application/pdf'),
        ];
    }

    /**
     * Genera el PDF de la factura
     */
    private function generatePDF($factura)
    {
        // Cargar relaciones necesarias para el PDF
        $facturaCompleta = \App\Models\ElectronicInvoice::with([
            'user.company',
            'buyer',
            'invoiceDetails.item.taxes',
            'invoiceDetails.item.measurementUnit',
            'electronicDocuments'
        ])->find($factura->id);

        // Opciones para DomPDF para mejor manejo de imágenes
        $options = [
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => false, // Deshabilitar carga de imágenes remotas para evitar dependencia GD/Imagick
            'isHtml5ParserEnabled' => true
        ];

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $facturaCompleta,
            'enableImages' => false
        ])
            ->setPaper('letter')
            ->setOptions($options);

        return $pdf->output();
    }
}
