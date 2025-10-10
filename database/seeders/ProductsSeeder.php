<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\MeasurementUnit;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_CO');

        // Obtener solo las unidades de medida aplicables a productos
        $productUnits = MeasurementUnit::where('application_type', 'Product')->get();

        for ($i = 1; $i <= 20; $i++) {
            DB::table('products')->insert([
                // Llave foránea hacia measurement_units
                'measurement_unit_id' => $productUnits->random()->id,

                // Campos principales (en inglés, según la migración)
                'standard_code' => $faker->optional()->numerify('#####'), // puede ser null
                'product_code' => Str::upper(Str::random(10)),
                'name' => ucfirst($faker->unique()->word()) . ' ' . $faker->numberBetween(1, 10),
                'description' => $faker->optional()->sentence(),
                'unit_price' => $faker->randomFloat(2, 5000, 500000),
                'status' => 'Active',

                // Timestamps
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
