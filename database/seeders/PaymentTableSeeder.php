<?php

namespace Database\Seeders;
use App\Models\Payment;

use Illuminate\Database\Seeder;


class PaymentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usa el factory para crear 50 registros de pagos
        Payment::factory()->count(50)->create();
    }
}

