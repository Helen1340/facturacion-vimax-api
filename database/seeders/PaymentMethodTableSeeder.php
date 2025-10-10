<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodTableSeeder extends Seeder
{
    public function run(): void
    {
        // 1️⃣ Limpiar la tabla para evitar duplicados (opcional en producción)
        DB::table('payment_methods')->delete();

        // 2️⃣ Insertar métodos de pago oficiales según la DIAN (Anexo Técnico 1.9)
        DB::table('payment_methods')->insert([
            [
                'name' => 'Cash',
                'dian_code' => '10',
                'description' => 'Payment in cash',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Credit Card',
                'dian_code' => '42',
                'description' => 'Payment using credit card',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Debit Card',
                'dian_code' => '41',
                'description' => 'Payment using debit card',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bank Transfer',
                'dian_code' => '47',
                'description' => 'Transfer to bank account',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Check',
                'dian_code' => '21',
                'description' => 'Payment by check',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
