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
                'nit' => '900123456-1',
                'nombre_comercial' => 'Empresa Uno',
                'direccion' => 'Calle Falsa 123',
                'ciudad' => 'Bogotá',
                'departamento' => 'Cundinamarca',
                'pais' => 'Colombia',
                'telefono' => '3101234567',
                'correo_electronico' => 'info1@empresa.com',
                'regimen' => 'Común',
                'logo_url' => null,
                'codigo_ciiu' => '6201',
                'representante_nombre' => 'Juan Pérez',
                'representante_tipo_documento' => 'CC',
                'representante_numero_documento' => '1234567890',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'razon_social' => 'Empresa de Prueba 2 Ltda.',
                'nit' => '800987654-2',
                'nombre_comercial' => 'Empresa Dos',
                'direccion' => 'Avenida Siempre Viva 456',
                'ciudad' => 'Medellín',
                'departamento' => 'Antioquia',
                'pais' => 'Colombia',
                'telefono' => '3209876543',
                'correo_electronico' => 'info2@empresa.com',
                'regimen' => 'Simplificado',
                'logo_url' => null,
                'codigo_ciiu' => '6202',
                'representante_nombre' => 'María Gómez',
                'representante_tipo_documento' => 'CC',
                'representante_numero_documento' => '9876543210',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'razon_social' => 'Comercializadora del Sur',
                'nit' => '900321654-3',
                'nombre_comercial' => 'Comercializadora del Sur',
                'direccion' => 'Carrera 70 #10-50',
                'ciudad' => 'Cali',
                'departamento' => 'Valle del Cauca',
                'pais' => 'Colombia',
                'telefono' => '3123334455',
                'correo_electronico' => 'info3@empresa.com',
                'regimen' => 'Común',
                'logo_url' => null,
                'codigo_ciiu' => '4700',
                'representante_nombre' => 'Carlos López',
                'representante_tipo_documento' => 'NIT',
                'representante_numero_documento' => '900321654',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'razon_social' => 'Innovaciones del Norte',
                'nit' => '800999888-4',
                'nombre_comercial' => 'Innovaciones',
                'direccion' => 'Calle 22 #5-15',
                'ciudad' => 'Barranquilla',
                'departamento' => 'Atlántico',
                'pais' => 'Colombia',
                'telefono' => '3154445566',
                'correo_electronico' => 'info4@empresa.com',
                'regimen' => 'Común',
                'logo_url' => null,
                'codigo_ciiu' => '6202',
                'representante_nombre' => 'Ana Torres',
                'representante_tipo_documento' => 'CE',
                'representante_numero_documento' => '123987456',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
