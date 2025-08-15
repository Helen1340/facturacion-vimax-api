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
                'id_payment' => 'P001',
                'fecha_pago' => '2025-08-10',
                'valor_pagado' => 150000.00,
                'moneda' => 'COP',
                'medio_pago' => 'Transferencia Bancaria'
            ],
            [
                'id_payment' => 'P002',
                'fecha_pago' => '2025-08-11',
                'valor_pagado' => 200.50,
                'moneda' => 'USD',
                'medio_pago' => 'Tarjeta de Crédito'
            ],
            [
                'id_payment' => 'P003',
                'fecha_pago' => null,
                'valor_pagado' => 75000.00,
                'moneda' => 'COP',
                'medio_pago' => 'Efectivo'
            ],
            [
                'id_payment' => 'P004',
                'fecha_pago' => '2025-08-12',
                'valor_pagado' => 1250.75,
                'moneda' => 'USD',
                'medio_pago' => 'Cheque'
            ],
            [
                'id_payment' => 'P005',
                'fecha_pago' => '2025-08-14',
                'valor_pagado' => 500000.00,
                'moneda' => 'COP',
                'medio_pago' => 'Pago en Línea'
            ]
        ]);
    }
}
