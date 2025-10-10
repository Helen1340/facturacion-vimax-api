<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TaxTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('taxes')->insert([
            // Impuesto sobre las Ventas (IVA)
            [
                'tax_code' => 'IVA19',
                'name' => 'IVA 19%',
                'description' => 'Impuesto sobre las ventas a la tarifa del 19%',
                'type' => 'IVA',
                'percentage' => 19.00,
                'fixed_value' => null,
                'application_type' => 'Porcentaje',
                'min_value' => null,
                'max_value' => null,
                'status' => 'Activo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tax_code' => 'IVA05',
                'name' => 'IVA 5%',
                'description' => 'Impuesto sobre las ventas a la tarifa del 5%',
                'type' => 'IVA',
                'percentage' => 5.00,
                'fixed_value' => null,
                'application_type' => 'Porcentaje',
                'min_value' => null,
                'max_value' => null,
                'status' => 'Activo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tax_code' => 'IVA00',
                'name' => 'IVA Exento',
                'description' => 'Bienes y servicios exentos de IVA',
                'type' => 'IVA',
                'percentage' => 0.00,
                'fixed_value' => null,
                'application_type' => 'Porcentaje',
                'min_value' => null,
                'max_value' => null,
                'status' => 'Activo',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Impuesto Nacional al Consumo (INC)
            [
                'tax_code' => 'INC08',
                'name' => 'Impuesto Nacional al Consumo 8%',
                'description' => 'Impuesto Nacional al Consumo aplicable a restaurantes y bares',
                'type' => 'INC',
                'percentage' => 8.00,
                'fixed_value' => null,
                'application_type' => 'Porcentaje',
                'min_value' => null,
                'max_value' => null,
                'status' => 'Activo',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Retención en la fuente
            [
                'tax_code' => 'RETFUENTE025',
                'name' => 'Retención en la fuente 2.5%',
                'description' => 'Retención en la fuente por compras generales',
                'type' => 'RETENCION',
                'percentage' => 2.50,
                'fixed_value' => null,
                'application_type' => 'Retencion',
                'min_value' => null,
                'max_value' => null,
                'status' => 'Activo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
