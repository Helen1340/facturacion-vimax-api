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

        // Obtener solo las unidades de medida aplicables a servicios
        $serviceUnits = MeasurementUnit::where('application_type', 'Service')->get();

        for ($i = 1; $i <= 10; $i++) {
            DB::table('services')->insert([
                'measurement_unit_id' => $serviceUnits->random()->id, // Unidad de medida

                'service_code' => strtoupper(Str::random(8)), // Código interno del servicio (único)
                'name' => ucfirst($faker->words(2, true)), // Nombre del servicio
                'description' => $faker->sentence(6), // Descripción del servicio
                'unit_price' => $faker->randomFloat(2, 20000, 1500000), // Precio unitario
                'status' => 'Active', // Estado

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
