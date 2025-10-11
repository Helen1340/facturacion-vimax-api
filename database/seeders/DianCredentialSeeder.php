<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DianCredential;

class DianCredentialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DianCredential::create([
            'company_id' => 1,
            'ambiente' => 'pruebas',
            'url_point' => 'https://vpfe-hab.dian.gov.co/WcfDianCustomerServices.svc',
            'usuario' => '123456789',
            'password' => 'clavePrueba123',
            'estado' => 'Activo',
        ]);
    }
}
