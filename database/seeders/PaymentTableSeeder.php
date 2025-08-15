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
                'fecha_pago' => '2025-08-10',
                'valor_pagado' => 150000.00,
                'moneda' => 'COP',
                'medio_pago' => 'Transferencia Bancaria'
            ],
            [
                'fecha_pago' => '2025-08-11',
                'valor_pagado' => 200.50,
                'moneda' => 'USD',
                'medio_pago' => 'Tarjeta de Crédito'
            ],
            [
                'fecha_pago' => null,
                'valor_pagado' => 75000.00,
                'moneda' => 'COP',
                'medio_pago' => 'Efectivo'
            ],
            [
                'fecha_pago' => '2025-08-12',
                'valor_pagado' => 1250.75,
                'moneda' => 'USD',
                'medio_pago' => 'Cheque'
            ],
            [
                'fecha_pago' => '2025-08-14',
                'valor_pagado' => 500000.00,
                'moneda' => 'COP',
                'medio_pago' => 'Pago en Línea'
            ]
        ]);
    }
}
