<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class DigitalCertificateTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_CO');
        $companies = Company::all();

        foreach ($companies as $company) {
            // Primer certificado (Vigente)
            DB::table('digital_certificates')->insert([
                'company_id' => $company->id,
                'nombre_certificado' => 'Certificado Principal ' . $company->id,
                'ruta_certificado' => '/certs/' . Str::slug($company->razon_social) . '-principal.p12',
                'numero_serial' => $faker->unique()->sha1(),
                'contrasena' => bcrypt('test_password'),
                'fecha_inicio' => $faker->date('Y-m-d', '-1 year'),
                'fecha_fin' => $faker->date('Y-m-d', '+1 year'),
                'estado' => 'Vigente',
                'entidad_emisora' => 'DIAN',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Segundo certificado (Vencido)
            DB::table('digital_certificates')->insert([
                'company_id' => $company->id,
                'nombre_certificado' => 'Certificado Secundario ' . $company->id,
                'ruta_certificado' => '/certs/' . Str::slug($company->razon_social) . '-secundario.p12',
                'numero_serial' => $faker->unique()->sha1(),
                'contrasena' => bcrypt('old_password'),
                'fecha_inicio' => $faker->date('Y-m-d', '-2 years'),
                'fecha_fin' => $faker->date('Y-m-d', '-1 month'),
                'estado' => 'Vencido',
                'entidad_emisora' => 'DIAN',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}