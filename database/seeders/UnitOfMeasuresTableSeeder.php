<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitOfMeasuresTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('unit_of_measures')->insert([
            [
                'IdUnidadMedida' => 1,
                'Nombre' => 'Unidad',
                'Estado' => true,
                'CodioDIAN' => 'UND',
                'Descripcion' => 'Unidad estándar de medida'
            ],
            [
                'IdUnidadMedida' => 2,
                'Nombre' => 'Kilogramo',
                'Estado' => true,
                'CodioDIAN' => 'KGM',
                'Descripcion' => 'Medida de peso equivalente a 1000 gramos'
            ],
            [
                'IdUnidadMedida' => 3,
                'Nombre' => 'Metro',
                'Estado' => true,
                'CodioDIAN' => 'MTR',
                'Descripcion' => 'Medida de longitud'
            ],
            [
                'IdUnidadMedida' => 4,
                'Nombre' => 'Litro',
                'Estado' => true,
                'CodioDIAN' => 'LTR',
                'Descripcion' => 'Medida de volumen de líquidos'
            ],
            [
                'IdUnidadMedida' => 5,
                'Nombre' => 'Caja',
                'Estado' => true,
                'CodioDIAN' => 'CJ',
                'Descripcion' => 'Unidad que representa un empaque con varios productos'
            ],
        ]);
    }
}
