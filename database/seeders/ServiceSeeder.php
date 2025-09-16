<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\MeasurementUnit;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_CO');
        // Obtener solo las unidades de medida para servicios
        $serviceUnits = MeasurementUnit::where('tipo_aplicacion', 'Servicio')->get();

        for ($i = 1; $i <= 10; $i++) {
            DB::table('services')->insert([
                // Asignar una unidad de medida aleatoria solo de las de servicios
                'measurement_unit_id' => $serviceUnits->random()->id,
                'nombre' => $faker->unique()->sentence(2),
                'descripcion' => $faker->sentence(5),
                'codigo_servicio' => Str::upper(Str::random(5)),
                'precio_unitario' => $faker->randomFloat(2, 10000, 1000000),
                'estado' => 'Activo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}