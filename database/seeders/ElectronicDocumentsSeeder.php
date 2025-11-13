<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ElectronicInvoice;
use App\Models\CreditDebitNote;
use App\Models\DianNumbering;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class ElectronicDocumentsSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_CO');

        // 🔹 Obtener numeraciones DIAN (por si se requiere asociar)
        $numberings = DianNumbering::all();

        // 🔹 Obtener facturas y notas existentes
        $invoices = ElectronicInvoice::all();
        $notes = CreditDebitNote::all();

        // --- Crear documentos electrónicos para facturas ---
        foreach ($invoices as $invoice) {
            $dianNumbering = $numberings->random() ?? null;
            $this->createElectronicDocumentEntry(
                $invoice->id,
                null,
                $dianNumbering?->id,
                'Factura Electrónica'
            );
        }

        // --- Crear documentos electrónicos para notas crédito/débito ---
        foreach ($notes as $note) {
            $dianNumbering = $numberings->random() ?? null;
            $this->createElectronicDocumentEntry(
                $note->electronic_invoice_id,
                $note->id,
                $dianNumbering?->id,
                "Nota de " . ucfirst($note->document_type)
            );
        }
    }

    private function createElectronicDocumentEntry($invoiceId, $noteId, $dianNumberingId, $docType)
    {
        $faker = Faker::create('es_CO');
        $cufe = Str::uuid()->toString();
        $cude = Str::uuid()->toString();

        // Simular contenido XML y CDR
        $xmlContent = '<ElectronicDocument><CUFE>' . $cufe . '</CUFE></ElectronicDocument>';
        $cdrContent = '<CDR><Estado>Aprobado</Estado><CUFE>' . $cufe . '</CUFE></CDR>';
        $qrCode = "https://catalogo-vpfe.dian.gov.co/document/" . $cufe;

        DB::table('electronic_documents')->insert([
            'electronic_invoice_id' => $invoiceId,
            'credit_debit_note_id' => $noteId,
            'dian_numbering_id' => $dianNumberingId,
            'cufe' => $cufe,
            'cude' => $cude,
            'xml_document' => $xmlContent,
            'dian_status' => $faker->randomElement(['Aprobado', 'Rechazado', 'En Proceso']),
            'validation_date' => now(),
            'digital_signature' => Str::random(50),
            'document_hash' => hash('sha256', $xmlContent),
            'description' => "Documento electrónico generado automáticamente para " . $docType,
            'environment' => $faker->randomElement(['HABILITACION', 'PRODUCCION']),
            'document_type' => $docType,
            'qr_code' => $qrCode,
            'cdr' => $cdrContent,
            'emission_mode' => $faker->randomElement(['normal', 'en contingencia']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
