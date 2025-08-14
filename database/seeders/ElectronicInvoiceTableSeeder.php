<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\ElectronicInvoice;

class ElectronicInvoiceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $electronicInvoices = [
            [
                'user_id'        => 1,
                'customer_id'    => 1,
                'numero_factura' => 'FE-0001',
                'fecha_emision'  => Carbon::now()->format('Y-m-d'),
                'hora_emision'   => Carbon::now()->format('H:i:s'),
                'moneda'         => 'COP',
                'medio_pago'     => 'Efectivo',
                'subtotal'       => 100000,
                'total_impuesto' => 19000,
                'total'          => 119000,
                'cufe'           => '7C8F4B6E3A1D2F0B9A6E8D7C4F3B2A1D6E7C8F9B0A1D2F3B4C5E6F7A8B9C0D1',
                'codigo_qr'      => null,
                'xml_firmado'    => '<xml>Factura 1</xml>',
                'estado_dian'    => 'pendiente',
                'cdr'            => null,
                'modo_emision'   => 'normal',
                'estado_interno' => 'borrador',
            ],
            [
                'user_id'        => 1,
                'customer_id'    => 2,
                'numero_factura' => 'FE-0002',
                'fecha_emision'  => Carbon::now()->format('Y-m-d'),
                'hora_emision'   => Carbon::now()->format('H:i:s'),
                'moneda'         => 'COP',
                'medio_pago'     => 'Tarjeta',
                'subtotal'       => 50000,
                'total_impuesto' => 9500,
                'total'          => 59500,
                'cufe'           => '1A2B3C4D5E6F7G8H9I0J1K2L3M4N5O6P7Q8R9S0T1U2V3W4X5Y6Z7A8B9C0D1E2',
                'codigo_qr'      => null,
                'xml_firmado'    => '<xml>Factura 2</xml>',
                'estado_dian'    => 'pendiente',
                'cdr'            => null,
                'modo_emision'   => 'normal',
                'estado_interno' => 'borrador',
            ],
            [
                'user_id'        => 2,
                'customer_id'    => 3,
                'numero_factura' => 'FE-0003',
                'fecha_emision'  => Carbon::now()->format('Y-m-d'),
                'hora_emision'   => Carbon::now()->format('H:i:s'),
                'moneda'         => 'COP',
                'medio_pago'     => 'Transferencia',
                'subtotal'       => 75000,
                'total_impuesto' => 14250,
                'total'          => 89250,
                'cufe'           => 'F1E2D3C4B5A697887766554433221100FFEEDDCCBBAA99887766554433221100',
                'codigo_qr'      => null,
                'xml_firmado'    => '<xml>Factura 3</xml>',
                'estado_dian'    => 'pendiente',
                'cdr'            => null,
                'modo_emision'   => 'normal',
                'estado_interno' => 'borrador',
            ],
            [
                'user_id'        => 2,
                'customer_id'    => 4,
                'numero_factura' => 'FE-0004',
                'fecha_emision'  => Carbon::now()->format('Y-m-d'),
                'hora_emision'   => Carbon::now()->format('H:i:s'),
                'moneda'         => 'COP',
                'medio_pago'     => 'Efectivo',
                'subtotal'       => 120000,
                'total_impuesto' => 22800,
                'total'          => 142800,
                'cufe'           => 'ABCDEF1234567890ABCDEF1234567890ABCDEF1234567890ABCDEF1234567890',
                'codigo_qr'      => null,
                'xml_firmado'    => '<xml>Factura 4</xml>',
                'estado_dian'    => 'pendiente',
                'cdr'            => null,
                'modo_emision'   => 'normal',
                'estado_interno' => 'borrador',
            ],
            [
                'user_id'        => 3,
                'customer_id'    => 5,
                'numero_factura' => 'FE-0005',
                'fecha_emision'  => Carbon::now()->format('Y-m-d'),
                'hora_emision'   => Carbon::now()->format('H:i:s'),
                'moneda'         => 'COP',
                'medio_pago'     => 'Tarjeta',
                'subtotal'       => 60000,
                'total_impuesto' => 11400,
                'total'          => 71400,
                'cufe'           => '9876543210ABCDEF9876543210ABCDEF9876543210ABCDEF9876543210ABCDEF',
                'codigo_qr'      => null,
                'xml_firmado'    => '<xml>Factura 5</xml>',
                'estado_dian'    => 'pendiente',
                'cdr'            => null,
                'modo_emision'   => 'normal',
                'estado_interno' => 'borrador',
            ],
        ];

        foreach ($electronicInvoices as $electronicInvoice) {
            ElectronicInvoice::create($electronicInvoice);
        }
    
    }
}
