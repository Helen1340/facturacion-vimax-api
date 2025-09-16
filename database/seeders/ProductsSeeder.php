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
        $productUnits = MeasurementUnit::where('tipo_aplicacion', 'Producto')->get();

        for ($i = 1; $i <= 20; $i++) {
            DB::table('products')->insert([
                // Asignar una unidad de medida aleatoria de la lista filtrada
                'measurement_unit_id' => $productUnits->random()->id,
                'codigo_estandar' => $faker->unique()->numerify('#####'),
                'codigo_producto' => Str::upper(Str::random(10)),
                'nombre' => $faker->unique()->word() . ' ' . $faker->numberBetween(1, 10),
                'descripcion' => $faker->sentence(),
                'precio_unitario' => $faker->randomFloat(2, 5000, 500000),
                'estado' => 'Activo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}