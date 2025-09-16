<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use Faker\Factory as Faker;

class DianNumberingTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_CO');
        $companies = Company::all();

        foreach ($companies as $company) {
            // Primer rango de numeración para Factura
            DB::table('dian_numberings')->insert([
                'company_id' => $company->id,
                'tipo_documento' => 'Factura',
                'prefijo' => strtoupper($faker->lexify('F?')),
                'numero_inicio' => 1,
                'numero_fin' => 5000,
                'fecha_resolucion' => $faker->date(),
                'numero_resolucion' => $faker->unique()->numerify('##########'),
                'fecha_inicio' => $faker->date(),
                'fecha_fin' => $faker->date('Y-m-d', '+2 years'),
                'estado_actual' => 'Activo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Segundo rango de numeración para NotaCredito
            DB::table('dian_numberings')->insert([
                'company_id' => $company->id,
                'tipo_documento' => 'NotaCredito',
                'prefijo' => strtoupper($faker->lexify('NC?')),
                'numero_inicio' => 1,
                'numero_fin' => 500,
                'fecha_resolucion' => $faker->date(),
                'numero_resolucion' => $faker->unique()->numerify('##########'),
                'fecha_inicio' => $faker->date(),
                'fecha_fin' => $faker->date('Y-m-d', '+1 year'),
                'estado_actual' => 'Activo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}