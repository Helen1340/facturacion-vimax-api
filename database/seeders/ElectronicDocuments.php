<?php

namespace Database\Seeders;

use App\Models\ElectronicDocument;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ElectronicDocuments extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ElectronicDocument::insert([
            [
                'ElectronicInvoice_id' => 1,
                'DianNumbering_id' => 1,
                'CreditDebitNote_id' => null,
                'cufe' => 'CUFE1234567890',
                'cude' => 'DOC-001',
                'xml_documento' => '<xml>Documento 1</xml>',
                'estado_dian' => 'Enviado',
                'fecha_validacion' => '2025-08-01',
                'firma_digital' => 'FirmaDigital001',
                'hash_documento' => 'HASH001',
                'descripcion' => 'Factura electrónica de prueba 1',
                'ambiente' => 'Producción',
                'tipo_documento' => 'Factura',
                'cdr' => 'CDR001',
                'qr_codigo' => 'QRCODE001',
                'modo_emision' => 'normal',
            ],
            [
                'ElectronicInvoice_id' => 2,
                'DianNumbering_id' => 1,
                'CreditDebitNote_id' => null,
                'cufe' => 'CUFE9876543210',
                'cude' => 'DOC-002',
                'xml_documento' => '<xml>Documento 2</xml>',
                'estado_dian' => 'Aceptado',
                'fecha_validacion' => '2025-08-02',
                'firma_digital' => 'FirmaDigital002',
                'hash_documento' => 'HASH002',
                'descripcion' => 'Factura electrónica de prueba 2',
                'ambiente' => 'Pruebas',
                'tipo_documento' => 'Factura',
                'cdr' => 'CDR002',
                'qr_codigo' => 'QRCODE002',
                'modo_emision' => 'en contingencia',
            ],
            [
                'ElectronicInvoice_id' => 3,
                'DianNumbering_id' => 2,
                'CreditDebitNote_id' => null,
                'cufe' => 'CUFE1122334455',
                'cude' => 'DOC-003',
                'xml_documento' => '<xml>Documento 3</xml>',
                'estado_dian' => 'Rechazado',
                'fecha_validacion' => '2025-08-03',
                'firma_digital' => 'FirmaDigital003',
                'hash_documento' => 'HASH003',
                'descripcion' => 'Factura electrónica de prueba 3',
                'ambiente' => 'Producción',
                'tipo_documento' => 'Factura',
                'cdr' => 'CDR003',
                'qr_codigo' => 'QRCODE003',
                'modo_emision' => 'normal',
            ],
            [
                'ElectronicInvoice_id' => 4,
                'DianNumbering_id' => 2,
                'CreditDebitNote_id' => 1,
                'cufe' => 'CUFE5566778899',
                'cude' => 'DOC-004',
                'xml_documento' => '<xml>Documento 4</xml>',
                'estado_dian' => 'Pendiente',
                'fecha_validacion' => '2025-08-04',
                'firma_digital' => 'FirmaDigital004',
                'hash_documento' => 'HASH004',
                'descripcion' => 'Nota crédito asociada',
                'ambiente' => 'Pruebas',
                'tipo_documento' => 'Nota Crédito',
                'cdr' => 'CDR004',
                'qr_codigo' => 'QRCODE004',
                'modo_emision' => 'normal',
            ],
            [
                'ElectronicInvoice_id' => 5,
                'DianNumbering_id' => 3,
                'CreditDebitNote_id' => 2,
                'cufe' => 'CUFE0001112223',
                'cude' => 'DOC-005',
                'xml_documento' => '<xml>Documento 5</xml>',
                'estado_dian' => 'Aceptado',
                'fecha_validacion' => '2025-08-05',
                'firma_digital' => 'FirmaDigital005',
                'hash_documento' => 'HASH005',
                'descripcion' => 'Nota débito asociada',
                'ambiente' => 'Producción',
                'tipo_documento' => 'Nota Débito',
                'cdr' => 'CDR005',
                'qr_codigo' => 'QRCODE005',
                'modo_emision' => 'en contingencia',
            ],
        ]);
    }
}
