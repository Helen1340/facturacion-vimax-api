<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::create([
            'nit' => '900123456',
            'razon_social' => 'Empresa Demo S.A.',
            'tipo_documento' => 'NIT',
            'direccion' => 'Calle 123',
            'municipio' => 'Santander de Quilichao',
            'departamento' => 'Cauca',
            'pais' => 'Colombia',
            'telefono' => '3001234567',
            'correo_electronico' => 'demo@empresa.com',
            'regimen' => 'Común',
            'logo' => null,
            'codigo_ciiu' => '6201',
        ]);

        Company::create([
            'nit' => '900234567',
            'razon_social' => 'Servicios Integrales S.A.S.',
            'tipo_documento' => 'NIT',
            'direccion' => 'Carrera 45 # 67-89',
            'municipio' => 'Santander de Quilichao',
            'departamento' => 'Cauca',
            'pais' => 'Colombia',
            'telefono' => '3109876543',
            'correo_electronico' => 'contacto@servicios.com',
            'regimen' => 'Simplificado',
            'logo' => null,
            'codigo_ciiu' => '6202',
        ]);

        Company::create([
            'nit' => '900345678',
            'razon_social' => 'Tech Solutions Ltda.',
            'tipo_documento' => 'NIT',
            'direccion' => 'Avenida 10 # 20-30',
            'municipio' => 'Caloto',
            'departamento' => 'Cauca',
            'pais' => 'Colombia',
            'telefono' => '3112233445',
            'correo_electronico' => 'info@techsolutions.com',
            'regimen' => 'Común',
            'logo' => null,
            'codigo_ciiu' => '6203',
        ]);

        Company::create([
            'nit' => '900456789',
            'razon_social' => 'Alimentos y Bebidas S.A.',
            'tipo_documento' => 'NIT',
            'direccion' => 'Calle 50 # 12-34',
            'municipio' => 'Jamundi',
            'departamento' => 'Valle del Cauca',
            'pais' => 'Colombia',
            'telefono' => '3123344556',
            'correo_electronico' => 'ventas@alimentosybebidas.com',
            'regimen' => 'Común',
            'logo' => null,
            'codigo_ciiu' => '1101',
        ]);

        Company::create([
            'nit' => '900567890',
            'razon_social' => 'Construcciones Modernas S.A.S.',
            'tipo_documento' => 'NIT',
            'direccion' => 'Carrera 70 # 15-20',
            'municipio' => 'Santander de Quilichao',
            'departamento' => 'Cauca',
            'pais' => 'Colombia',
            'telefono' => '3134455667',
            'correo_electronico' => 'contacto@construccionesmodernas.com',
            'regimen' => 'Simplificado',
            'logo' => null,
            'codigo_ciiu' => '4101',
        ]);
    }
}




