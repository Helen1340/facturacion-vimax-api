<?php

namespace Database\Seeders;

use App\Models\ElectronicInvoice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ElectronicInvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usa el factory para crear 50 registros de la compañía
        ElectronicInvoice::factory()->count(50)->create();
    }
}
