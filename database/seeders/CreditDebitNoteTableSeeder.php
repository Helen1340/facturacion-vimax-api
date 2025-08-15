<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreditDebitNoteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('credit_debit_notes')->insert([
            [
                'Motivo'       => 'Devolución producto defectuoso',
                'tipo_nota'     => 'Credito',
                'descripcion'  => 'Nota de crédito por devolución de producto defectuoso',
                'valor_total'   => 1500.50,
                'cufe_nota'     => 'CUFE-00001',
                'xml_firmado'   => '<xml>Nota 1</xml>',
                'estado_dian'   => 'aceptada',
                'fecha_emision' => '2025-08-14',
                'moneda'       => 'USD',
            ],
            [
                'motivo' => 'Cobro adicional por servicio',
                'tipo_nota' => 'debito',
                'descripcion' => 'Nota de débito por cobro adicional',
                'valor_total' => 250.00,
                'cufe_nota' => 'CUFE-00002',
                'xml_firmado' => '<xml>Nota 2</xml>',
                'estado_dian' => 'pendiente',
                'fecha_emision' => '2025-08-13',
                'moneda' => 'USD',
            ],
            [
                'motivo' => 'Ajuste contable',
                'tipo_nota' => 'Credito',
                'descripcion' => 'Nota de crédito por ajuste contable',
                'valor_total' => 5000.75,
                'cufe_nota' => 'CUFE-00003',
                'xml_firmado' => '<xml>Nota 3</xml>',
                'estado_dian' => 'aceptada',
                'fecha_emision' => '2025-08-12',
                'moneda' => 'BOB',
            ],
            [
                'motivo' => 'Intereses por retraso de pago',
                'tipo_nota' => 'debito',
                'descripcion' => 'Nota de débito por intereses por retraso',
                'valor_total' => 120.00,
                'cufe_nota' => 'CUFE-00004',
                'xml_firmado' => '<xml>Nota 4</xml>',
                'estado_dian' => 'rechazada',
                'fecha_emision' => '2025-08-10',
                'moneda' => 'USD',
            ],
            [
                'motivo' => 'Descuento promocional',
                'tipo_nota' => 'Credito',
                'descripcion' => 'Nota de crédito por descuento aplicado',
                'valor_total' => 300.00,
                'cufe_nota' => 'CUFE-00005',
                'xml_firmado' => '<xml>Nota 5</xml>',
                'estado_dian' => 'pendiente',
                'fecha_emision' => '2025-08-09',
                'moneda' => 'BOB',
            ],
        ]);
    }
}

