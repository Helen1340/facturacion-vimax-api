<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ElectronicInvoice;
use App\Models\CreditDebitNote;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class ElectronicDocumentsSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_CO');
        
        // Get all invoices that were created by a previous seeder
        $invoices = ElectronicInvoice::all();

        // --- Create Electronic Documents for each Invoice ---
        foreach ($invoices as $invoice) {
            $this->createElectronicDocumentEntry($invoice->id, null, 'Factura Electrónica');
        }

        // --- Create Electronic Documents for each Credit/Debit Note ---
        $notes = CreditDebitNote::all();
        foreach ($notes as $note) {
            $this->createElectronicDocumentEntry($note->electronic_invoice_id, $note->id, "Nota de " . ucfirst($note->tipo_documento));
        }
    }

    private function createElectronicDocumentEntry($invoiceId, $noteId, $docType)
    {
        $faker = Faker::create('es_CO');
        $cufe = Str::uuid()->toString();
        $xmlContent = '<DocumentoXML><CUFE>' . $cufe . '</CUFE></DocumentoXML>';
        $qrCode = "https://dian.gov.co/qr/" . $cufe;

        DB::table('electronic_documents')->insert([
            'electronic_invoice_id' => $invoiceId,
            'credit_debit_note_id' => $noteId,
            'cufe' => $cufe,
            'cude' => Str::uuid()->toString(),
            'xml_documento' => $xmlContent,
            'estado_dian' => $faker->randomElement(['Aprobado', 'Rechazado']),
            'fecha_validacion' => now(),
            'firma_digital' => Str::random(50),
            'hash_documento' => Str::random(150),
            'descripcion' => "Documento electrónico para " . $docType,
            'ambiente' => 'Pruebas',
            'tipo_documento' => $docType,
            'qr_codigo' => $qrCode,
            'cdr' => '<CDR><Estado>Aprobado</Estado></CDR>',
            'modo_emision' => 'normal',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}