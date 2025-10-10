<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MeasurementUnitTableSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Limpiar la tabla antes de insertar (opcional, según ambiente)
        DB::table('measurement_units')->delete();

        // 2. Insertar unidades oficiales DIAN — versión en inglés (coherente con migración)
        DB::table('measurement_units')->insert([
            // === PRODUCTS ===
            ['name' => 'Unit',       'status' => 'Active', 'dian_code' => 'UND', 'description' => 'Product unit', 'application_type' => 'Product'],
            ['name' => 'Kilogram',   'status' => 'Active', 'dian_code' => 'KGM', 'description' => 'Weight in kilograms', 'application_type' => 'Product'],
            ['name' => 'Gram',       'status' => 'Active', 'dian_code' => 'GRM', 'description' => 'Weight in grams', 'application_type' => 'Product'],
            ['name' => 'Liter',      'status' => 'Active', 'dian_code' => 'LTR', 'description' => 'Volume in liters', 'application_type' => 'Product'],
            ['name' => 'Milliliter', 'status' => 'Active', 'dian_code' => 'MLT', 'description' => 'Volume in milliliters', 'application_type' => 'Product'],
            ['name' => 'Box',        'status' => 'Active', 'dian_code' => 'BX',  'description' => 'Box of products', 'application_type' => 'Product'],

            // === SERVICES ===
            ['name' => 'Hour',       'status' => 'Active', 'dian_code' => 'HUR', 'description' => 'Service time in hours', 'application_type' => 'Service'],
            ['name' => 'Day',        'status' => 'Active', 'dian_code' => 'DAY', 'description' => 'Service time in days', 'application_type' => 'Service'],
            ['name' => 'Month',      'status' => 'Active', 'dian_code' => 'MON', 'description' => 'Service time in months', 'application_type' => 'Service'],
            ['name' => 'Service',    'status' => 'Active', 'dian_code' => 'E48', 'description' => 'Service provision unit', 'application_type' => 'Service'],
            ['name' => 'Contract',   'status' => 'Active', 'dian_code' => 'CNT', 'description' => 'Service contract unit', 'application_type' => 'Service'],
        ]);

        // 3. (Opcional) En entorno local, generar unidades adicionales ficticias
        // if (app()->environment('local')) {
        //     \App\Models\MeasurementUnit::factory()->count(10)->create();
        // }
    }
}
