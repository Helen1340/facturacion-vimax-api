<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
                'nombre' => 'IVA',
                'descripcion' => 'Impuesto sobre las ventas a la tarifa del 19%',
                'tipo' => 'IVA',
                'porcentaje_base' => 19.00,
                'estado' => 'Activo',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'nombre' => 'IVA',
                'descripcion' => 'Impuesto sobre las ventas a la tarifa del 5%',
                'tipo' => 'IVA',
                'porcentaje_base' => 5.00,
                'estado' => 'Activo',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'nombre' => 'IVA',
                'descripcion' => 'Bienes exentos de IVA',
                'tipo' => 'IVA',
                'porcentaje_base' => 0.00,
                'estado' => 'Activo',
                'created_at' => now(), 'updated_at' => now(),
            ],

            // Impuesto Nacional al Consumo (INC)
            [
                'nombre' => 'INC',
                'descripcion' => 'Impuesto Nacional al Consumo del 8%',
                'tipo' => 'INC',
                'porcentaje_base' => 8.00,
                'estado' => 'Activo',
                'created_at' => now(), 'updated_at' => now(),
            ],

            // Retenciones (ejemplo)
            [
                'nombre' => 'Retefuente',
                'descripcion' => 'Retención en la fuente por compras',
                'tipo' => 'RETENCION',
                'porcentaje_base' => 2.50,
                'estado' => 'Activo',
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);
    }
}