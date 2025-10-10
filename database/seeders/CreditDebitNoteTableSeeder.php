<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\ElectronicInvoice;

class CreditDebitNoteTableSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            // Obtener hasta dos facturas asociadas a esta compañía
            $invoices = ElectronicInvoice::whereHas('user', function ($query) use ($company) {
                $query->where('company_id', $company->id);
            })->inRandomOrder()->take(2)->get();

            if ($invoices->count() >= 2) {

                /** ==========================
                 * Nota de CRÉDITO (credit note)
                 * ===========================*/
                $invoice1 = $invoices->first();

                DB::table('credit_debit_notes')->insert([
                    'electronic_invoice_id' => $invoice1->id,  // Relación con la factura electrónica
                    'reason' => 'Invoice cancellation due to registration error', // Motivo de la nota
                    'note_type' => 'credit',  // Tipo de nota: crédito
                    'note_number' => 'CN-' . $invoice1->invoice_number, // Número de la nota
                    'status' => 'accepted',  // Estado actual
                    'issue_date' => now(),  // Fecha de emisión
                    'total_amount' => $invoice1->payable_amount ?? 0,  // Valor total corregido
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                /** ==========================
                 * Nota de DÉBITO (debit note)
                 * ===========================*/
                $invoice2 = $invoices->last();

                DB::table('credit_debit_notes')->insert([
                    'electronic_invoice_id' => $invoice2->id,
                    'reason' => 'Adjustment for shipping surcharge', // Ajuste por recargo de envío
                    'note_type' => 'debit',
                    'note_number' => 'DN-' . $invoice2->invoice_number,
                    'status' => 'accepted',
                    'issue_date' => now(),
                    'total_amount' => 50000.00, // Monto del ajuste
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
