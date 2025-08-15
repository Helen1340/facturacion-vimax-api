<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payment')->insert([
            [
                'IdPayment' => 'P001',
                'Numero_Factura' => 'F001',
                'FechaPago' => '2025-08-10',
                'ValorPagado' => 150000.00,
                'Moneda' => 'COP',
                'MedioPago' => 'Transferencia Bancaria'
            ],
            [
                'IdPayment' => 'P002',
                'Numero_Factura' => 'F002',
                'FechaPago' => '2025-08-11',
                'ValorPagado' => 200.50,
                'Moneda' => 'USD',
                'MedioPago' => 'Tarjeta de Crédito'
            ],
            [
                'IdPaymet' => 'P003',
                'Numero_Factura' => 'F003',
                'FechaPago' => null,
                'ValorPagado' => 75000.00,
                'Moneda' => 'COP',
                'MedioPago' => 'Efectivo'
            ],
            [
                'IdPayment' => 'P004',
                'Numero_Factura' => 'F004',
                'FechaPago' => '2025-08-12',
                'ValorPagado' => 1250.75,
                'Moneda' => 'USD',
                'MedioPago' => 'Cheque'
            ],
            [
                'IdPayment' => 'P005',
                'Numero_Factura' => 'F005',
                'FechaPago' => '2025-08-14',
                'ValorPagado' => 500000.00,
                'Moneda' => 'COP',
                'MedioPago' => 'Pago en Línea'
            ]
        ]);
    }
}
