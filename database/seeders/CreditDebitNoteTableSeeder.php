<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CreditDebitNoteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('credit_debit_notes')->insert([
            // Nota 1: Nota Crédito
            [
                'motivo'           => 'Devolución de mercancía por cliente',
                'tipo_documento'   => 'credito',
                'descripcion'      => 'Crédito por devolución de 2 unidades de producto X',
                'numero_nota'      => 'NC-001',
                'estado'           => 'aceptada',
                'fecha_emision'    => '2024-07-10',
                'valor_total'      => 50.00,
            ],
            // Nota 2: Nota Débito
            [
                'motivo'           => 'Intereses por mora en pago',
                'tipo_documento'   => 'debito',
                'descripcion'      => 'Cargo adicional por mora de 30 días en factura #12345',
                'numero_nota'      => 'ND-001',
                'estado'           => 'aceptada',
                'fecha_emision'    => '2024-07-15',
                'valor_total'      => 15.75,
            ],
            // Nota 3: Nota Crédito pendiente
            [
                'motivo'           => 'Ajuste por error en facturación',
                'tipo_documento'   => 'credito',
                'descripcion'      => 'Corrección de precio en factura #67890',
                'numero_nota'      => 'NC-002',
                'estado'           => 'pendiente',
                'fecha_emision'    => '2024-07-20',
                'valor_total'      => 25.50,
            ],
            // Nota 4: Nota Débito rechazada
            [
                'motivo'           => 'Reembolso por servicio no prestado',
                'tipo_documento'   => 'debito',
                'descripcion'      => 'Rechazo de solicitud de reembolso por servicio XXX',
                'numero_nota'      => 'ND-002',
                'estado'           => 'rechazada',
                'fecha_emision'    => '2024-07-25',
                'valor_total'      => 75.00,
            ],
        ]);
    }
}
