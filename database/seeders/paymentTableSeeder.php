<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payments')->insert([
            // Registro 1: Pago con tarjeta de crédito
            [
                'id' => 1,
                'ElectronicInvoice_id' => 1, // Suponiendo que existe la factura con ID 1
                'PaymentMethod_id' => 1,    // Suponiendo que existe el método con ID 1
                'fecha_pago' => '2025-01-15',
                'valor_pagado' => 1500.50,
                'moneda' => 'USD',
                'referencia_pago' => 'PAY-001',
            ],
            // Registro 2: Pago con transferencia bancaria
            [
                'id' => 2,
                'ElectronicInvoice_id' => 1,
                'PaymentMethod_id' => 2,
                'fecha_pago' => '2025-02-10',
                'valor_pagado' => 250.00,
                'moneda' => 'USD',
                'referencia_pago' => 'PAY-002',
            ],
            // Registro 3: Pago en efectivo
            [
                'id' => 3,
                'ElectronicInvoice_id' => 2,
                'PaymentMethod_id' => 3,
                'fecha_pago' => '2025-03-05',
                'valor_pagado' => 999.99,
                'moneda' => 'EUR',
                'referencia_pago' => 'PAY-003',
            ],
            // Registro 4: Pago parcial en pesos colombianos
            [
                'id' => 4,
                'ElectronicInvoice_id' => 3,
                'PaymentMethod_id' => 1,
                'fecha_pago' => '2025-04-20',
                'valor_pagado' => 300.00,
                'moneda' => 'COP',
                'referencia_pago' => 'PAY-004',
            ],
            // Registro 5: Pago final de una factura
            [
                'id' => 5,
                'ElectronicInvoice_id' => 4,
                'PaymentMethod_id' => 2,
                'fecha_pago' => '2025-05-01',
                'valor_pagado' => 1200.75,
                'moneda' => 'USD',
                'referencia_pago' => 'PAY-005',
            ],
        ]);
    }
}
