<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\InvoiceDetail;
use App\Models\ElectronicInvoice;

class InvoiceDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Opción A: crear detalles para facturas nuevas
        // InvoiceDetail::factory(50)->create();

        // Opción B: para cada factura existente generar entre 1 y 5 detalles
        ElectronicInvoice::all()->each(function ($invoice) {
            $count = rand(1, 5);
            InvoiceDetail::factory()->count($count)->create([
                'electronic_invoice_id' => $invoice->id,
            ]);
        });
    }
}
