<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ElectronicInvoice;
use App\Models\PaymentMethod;
use Faker\Factory as Faker;

class PaymentTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_CO');
        $invoices = ElectronicInvoice::all();
        $paymentMethods = PaymentMethod::all();

        // 1. Recorrer TODAS las facturas
        foreach ($invoices as $invoice) {
            $paymentMethod = $paymentMethods->random();
            $valorPagado = $invoice->total_factura;
            
            DB::table('payments')->insert([
                'electronic_invoice_id' => $invoice->id,
                'payment_method_id' => $paymentMethod->id,
                'fecha_pago' => $faker->date(),
                'valor_pagado' => $valorPagado,
                'moneda' => 'COP',
                'referencia_pago' => $faker->uuid(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}