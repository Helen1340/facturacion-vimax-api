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

            // 🔹 Certificado vigente (Producción)
            DB::table('digital_certificates')->insert([
                'company_id'            => $company->id,
                'certificate_name'      => 'Primary Digital Certificate - ' . $company->id,
                'certificate_path'      => '/certs/' . Str::slug($company->business_name) . '-primary.p12',
                'serial_number'         => strtoupper(Str::random(20)),
                'password'              => bcrypt('Prod@12345'),
                'start_date'            => $faker->date('Y-m-d', '-6 months'),
                'end_date'              => $faker->date('Y-m-d', '+1 year'),
                'status'                => 'Vigente',
                'issuer'                => 'Entidad Certificadora DIAN',
                'certificate_type'      => 'Producción',
                'signature_algorithm'   => 'SHA256withRSA',
                'uuid'                  => (string) Str::uuid(),
                'description'           => 'Certificado principal activo utilizado para la emisión de facturas electrónicas.',
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);

            // 🔹 Certificado vencido (Pruebas)
            DB::table('digital_certificates')->insert([
                'company_id'            => $company->id,
                'certificate_name'      => 'Test Digital Certificate - ' . $company->id,
                'certificate_path'      => '/certs/' . Str::slug($company->business_name) . '-test.pfx',
                'serial_number'         => strtoupper(Str::random(20)),
                'password'              => bcrypt('Test@12345'),
                'start_date'            => $faker->date('Y-m-d', '-2 years'),
                'end_date'              => $faker->date('Y-m-d', '-2 months'),
                'status'                => 'Vencido',
                'issuer'                => 'Entidad Certificadora DIAN',
                'certificate_type'      => 'Pruebas',
                'signature_algorithm'   => 'SHA256withRSA',
                'uuid'                  => (string) Str::uuid(),
                'description'           => 'Certificado de pruebas vencido utilizado en entorno de homologación.',
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);
        }
    }
}
