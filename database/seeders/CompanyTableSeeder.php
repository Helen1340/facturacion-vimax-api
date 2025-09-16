<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('companies')->insert([
            [
                'razon_social' => 'Empresa de Prueba 1 S.A.S.',
                'tipo_documento' => 'NIT',
                'numero_documento' => '900123456-1',
                'direccion' => 'Calle Falsa 123',
                'municipio' => 'Bogotá',
                'departamento' => 'Cundinamarca',
                'pais' => 'Colombia',
                'telefono' => '3101234567',
                'correo_electronico' => 'info1@empresa.com',
                'regimen' => 'Común',
                'logo_url' => null,
                'nombre_comercial' => 'Empresa Uno',
                'codigo_ciiu' => '6201',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'razon_social' => 'Empresa de Prueba 2 Ltda.',
                'tipo_documento' => 'NIT',
                'numero_documento' => '800987654-2',
                'direccion' => 'Avenida Siempre Viva 456',
                'municipio' => 'Medellín',
                'departamento' => 'Antioquia',
                'pais' => 'Colombia',
                'telefono' => '3209876543',
                'correo_electronico' => 'info2@empresa.com',
                'regimen' => 'Simplificado',
                'logo_url' => null,
                'nombre_comercial' => 'Empresa Dos',
                'codigo_ciiu' => '6202',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'razon_social' => 'Comercializadora del Sur',
                'tipo_documento' => 'NIT',
                'numero_documento' => '900321654-3',
                'direccion' => 'Carrera 70 #10-50',
                'municipio' => 'Cali',
                'departamento' => 'Valle del Cauca',
                'pais' => 'Colombia',
                'telefono' => '3123334455',
                'correo_electronico' => 'info3@empresa.com',
                'regimen' => 'Común',
                'logo_url' => null,
                'nombre_comercial' => 'Comercializadora del Sur',
                'codigo_ciiu' => '4700',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'razon_social' => 'Innovaciones del Norte',
                'tipo_documento' => 'NIT',
                'numero_documento' => '800999888-4',
                'direccion' => 'Calle 22 #5-15',
                'municipio' => 'Barranquilla',
                'departamento' => 'Atlántico',
                'pais' => 'Colombia',
                'telefono' => '3154445566',
                'correo_electronico' => 'info4@empresa.com',
                'regimen' => 'Común',
                'logo_url' => null,
                'nombre_comercial' => 'Innovaciones',
                'codigo_ciiu' => '6202',
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);
    }
}