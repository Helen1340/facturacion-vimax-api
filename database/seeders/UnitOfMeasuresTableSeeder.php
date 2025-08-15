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
                'id_unidad_medida' => 1,
                'nombre' => 'Unidad',
                'estado' => true,
                'codio_dian' => 'UND',
                'descripcion' => 'Unidad estándar de medida'
            ],
            [
                'id_unidad_medida' => 2,
                'nombre' => 'Kilogramo',
                'estado' => true,
                'codio_dian' => 'KGM',
                'descripcion' => 'Medida de peso equivalente a 1000 gramos'
            ],
            [
                'id_unidad_medida' => 3,
                'nombre' => 'Metro',
                'estado' => true,
                'codio_dian' => 'MTR',
                'descripcion' => 'Medida de longitud'
            ],
            [
                'id_unidad_medida' => 4,
                'nombre' => 'Litro',
                'estado' => true,
                'codio_dian' => 'LTR',
                'descripcion' => 'Medida de volumen de líquidos'
            ],
            [
                'id_unidad_medida' => 5,
                'nombre' => 'Caja',
                'estado' => true,
                'codio_dian' => 'CJ',
                'descripcion' => 'Unidad que representa un empaque con varios productos'
            ],
        ]);
    }
}
