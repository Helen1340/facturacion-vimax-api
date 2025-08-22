<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usa el factory para crear 50 registros de la compañía
        PaymentMethod::factory()->count(50)->create();
    }
}
