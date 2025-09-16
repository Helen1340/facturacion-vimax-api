<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('payment_methods')->insert([
            [
                'nombre' => 'Efectivo',
                'codigo_dian' => '10',
                'descripcion' => 'Pago en efectivo',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'nombre' => 'Tarjeta de Crédito',
                'codigo_dian' => '42',
                'descripcion' => 'Pago con tarjeta de crédito',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'nombre' => 'Tarjeta Débito',
                'codigo_dian' => '41',
                'descripcion' => 'Pago con tarjeta débito',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'nombre' => 'Transferencia Bancaria',
                'codigo_dian' => '47',
                'descripcion' => 'Transferencia a cuenta bancaria',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'nombre' => 'Cheque',
                'codigo_dian' => '21',
                'descripcion' => 'Pago con cheque',
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);
    }
}