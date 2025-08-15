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
                'IdUsuario'    => 'USR001',
                'Motivo'       => 'Devolución producto defectuoso',
                'TipoNota'     => 'Credito',
                'Descripcion'  => 'Nota de crédito por devolución de producto defectuoso',
                'ValorTotal'   => 1500.50,
                'CUFENota'     => 'CUFE-00001',
                'XMLFirmado'   => '<xml>Nota 1</xml>',
                'EstadoDian'   => 'aceptada',
                'FechaEmision' => '2025-08-14',
                'Moneda'       => 'USD',
            ],
            [
                'IdUsuario'    => 'USR002',
                'Motivo'       => 'Cobro adicional por servicio',
                'TipoNota'     => 'debito',
                'Descripcion'  => 'Nota de débito por cobro adicional',
                'ValorTotal'   => 250.00,
                'CUFENota'     => 'CUFE-00002',
                'XMLFirmado'   => '<xml>Nota 2</xml>',
                'EstadoDian'   => 'pendiente',
                'FechaEmision' => '2025-08-13',
                'Moneda'       => 'USD',
            ],
            [
                'IdUsuario'    => 'USR003',
                'Motivo'       => 'Ajuste contable',
                'TipoNota'     => 'Credito',
                'Descripcion'  => 'Nota de crédito por ajuste contable',
                'ValorTotal'   => 5000.75,
                'CUFENota'     => 'CUFE-00003',
                'XMLFirmado'   => '<xml>Nota 3</xml>',
                'EstadoDian'   => 'aceptada',
                'FechaEmision' => '2025-08-12',
                'Moneda'       => 'BOB',
            ],
            [
                'IdUsuario'    => 'USR001',
                'Motivo'       => 'Intereses por retraso de pago',
                'TipoNota'     => 'debito',
                'Descripcion'  => 'Nota de débito por intereses por retraso',
                'ValorTotal'   => 120.00,
                'CUFENota'     => 'CUFE-00004',
                'XMLFirmado'   => '<xml>Nota 4</xml>',
                'EstadoDian'   => 'rechazada',
                'FechaEmision' => '2025-08-10',
                'Moneda'       => 'USD',
            ],
            [
                'IdUsuario'    => 'USR002',
                'Motivo'       => 'Descuento promocional',
                'TipoNota'     => 'Credito',
                'Descripcion'  => 'Nota de crédito por descuento aplicado',
                'ValorTotal'   => 300.00,
                'CUFENota'     => 'CUFE-00005',
                'XMLFirmado'   => '<xml>Nota 5</xml>',
                'EstadoDian'   => 'pendiente',
                'FechaEmision' => '2025-08-09',
                'Moneda'       => 'BOB',
            ]
        ]);
    }
}

