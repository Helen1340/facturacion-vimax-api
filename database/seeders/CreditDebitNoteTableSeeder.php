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
            // Obtener dos facturas de esta empresa para crear las notas
            $invoices = ElectronicInvoice::whereHas('user', function ($query) use ($company) {
                $query->where('company_id', $company->id);
            })->inRandomOrder()->take(2)->get();
            
            if ($invoices->count() >= 2) {
                // Nota de crédito para la primera factura seleccionada
                $invoice1 = $invoices->first();
                DB::table('credit_debit_notes')->insert([
                    'electronic_invoice_id' => $invoice1->id,
                    'motivo' => 'Anulación de la factura',
                    'tipo_documento' => 'credito',
                    'descripcion' => 'Anulación total del valor de la factura por error en el registro.',
                    'numero_nota' => 'NC-' . $invoice1->numero_factura,
                    'estado' => 'aceptada',
                    'fecha_emision' => now(),
                    'valor_total' => $invoice1->total_factura,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Nota de débito para la segunda factura seleccionada
                $invoice2 = $invoices->last();
                DB::table('credit_debit_notes')->insert([
                    'electronic_invoice_id' => $invoice2->id,
                    'motivo' => 'Ajuste por recargo',
                    'tipo_documento' => 'debito',
                    'descripcion' => 'Ajuste por costos adicionales de envío no incluidos en el valor original.',
                    'numero_nota' => 'ND-' . $invoice2->numero_factura,
                    'estado' => 'aceptada',
                    'fecha_emision' => now(),
                    'valor_total' => 50000.00, // Ajuste manual
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}