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
            // Rango de numeración para Factura
            DB::table('dian_numberings')->insert([
                'company_id' => $company->id,
                'document_type' => 'Factura',
                'document_type_code' => '01',
                'prefix' => strtoupper($faker->lexify('F?')),
                'start_number' => 1,
                'end_number' => 5000,
                'resolution_date' => $faker->date(),
                'resolution_number' => $faker->unique()->numerify('##########'),
                'validity_start_date' => $faker->date(),
                'validity_end_date' => $faker->date('Y-m-d', '+2 years'),
                'current_status' => 'Activo',
                'environment' => 'Pruebas',
                'description' => 'Numeración autorizada para pruebas DIAN',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Rango de numeración para Nota de Crédito
            DB::table('dian_numberings')->insert([
                'company_id' => $company->id,
                'document_type' => 'NotaCredito',
                'document_type_code' => '91',
                'prefix' => strtoupper($faker->lexify('NC?')),
                'start_number' => 1,
                'end_number' => 500,
                'resolution_date' => $faker->date(),
                'resolution_number' => $faker->unique()->numerify('##########'),
                'validity_start_date' => $faker->date(),
                'validity_end_date' => $faker->date('Y-m-d', '+1 year'),
                'current_status' => 'Activo',
                'environment' => 'Pruebas',
                'description' => 'Numeración para notas crédito DIAN',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
