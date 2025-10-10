<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ElectronicInvoice;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentTableSeeder extends Seeder
{
    public function run(): void
    {
        $invoices = ElectronicInvoice::all();
        $methods = PaymentMethod::all();

        foreach ($invoices as $invoice) {
            // Aseguramos un valor numérico para amount_paid
            $amountPaid = $invoice->payable_amount ?? 0;

            // Si no hay método de pago, saltamos
            if ($methods->isEmpty()) {
                continue;
            }

            $paymentMethod = $methods->random();

            DB::table('payments')->insert([
                'electronic_invoice_id' => $invoice->id,
                'payment_method_id' => $paymentMethod->id,
                'payment_date' => now()->subDays(rand(0, 30)),
                'amount_paid' => $amountPaid, // nunca nulo
                'currency' => $invoice->document_currency_code ?? 'COP',
                'payment_reference' => Str::uuid(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
