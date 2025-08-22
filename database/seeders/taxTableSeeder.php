<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('taxes')->insert([
            // Registro 1: IVA general 19%
            [
                'id'          => 1,
                'Nombre'      => 'IVA 19%',
                'Descripcion' => 'Impuesto al valor agregado general',
                'Tipo'        => 'IVA',
                'Porcentaje'  => 19.00,
                'Estado'      => true,
            ],
            // Registro 2: IVA reducido 5%
            [
                'id'          => 2,
                'Nombre'      => 'IVA 5%',
                'Descripcion' => 'Impuesto al valor agregado reducido',
                'Tipo'        => 'IVA',
                'Porcentaje'  => 5.00,
                'Estado'      => true,
            ],
            // Registro 3: Exento de IVA
            [
                'id'          => 3,
                'Nombre'      => 'Exento de IVA',
                'Descripcion' => 'Productos y servicios exentos de IVA',
                'Tipo'        => 'EXENTO',
                'Porcentaje'  => 0.00,
                'Estado'      => true,
            ],
            // Registro 4: Retención en la fuente 2.5%
            [
                'id'          => 4,
                'Nombre'      => 'Retención en la fuente',
                'Descripcion' => 'Retención sobre pagos',
                'Tipo'        => 'RETENCION',
                'Porcentaje'  => 2.50,
                'Estado'      => true,
            ],
            // Registro 5: Impuesto al consumo 8%
            [
                'id'          => 5,
                'Nombre'      => 'Impuesto al consumo',
                'Descripcion' => 'Impuesto especial al consumo de bienes y servicios',
                'Tipo'        => 'CONSUMO',
                'Porcentaje'  => 8.00,
                'Estado'      => true,
            ],
        ]);
    }
}
