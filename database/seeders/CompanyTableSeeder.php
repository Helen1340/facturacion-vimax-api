<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('companies')->insert([
            [
                'business_name' => 'Empresa de Prueba 1 S.A.S.',
                'nit' => '900123456-1',
                'trade_name' => 'Empresa Uno',
                'address' => 'Calle Falsa 123',
                'city' => 'Bogotá',
                'department' => 'Cundinamarca',
                'country' => 'Colombia',
                'phone' => '3101234567',
                'email' => 'info1@empresa.com',
                'tax_regime' => 'Común',
                'logo_url' => null,
                'ciiu_code' => '6201',
                'legal_representative_name' => 'Juan Pérez',
                'legal_representative_document_type' => 'CC',
                'legal_representative_document_number' => '1234567890',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_name' => 'Empresa de Prueba 2 Ltda.',
                'nit' => '800987654-2',
                'trade_name' => 'Empresa Dos',
                'address' => 'Avenida Siempre Viva 456',
                'city' => 'Medellín',
                'department' => 'Antioquia',
                'country' => 'Colombia',
                'phone' => '3209876543',
                'email' => 'info2@empresa.com',
                'tax_regime' => 'Simplificado',
                'logo_url' => null,
                'ciiu_code' => '6202',
                'legal_representative_name' => 'María Gómez',
                'legal_representative_document_type' => 'CC',
                'legal_representative_document_number' => '9876543210',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_name' => 'Comercializadora del Sur',
                'nit' => '900321654-3',
                'trade_name' => 'Comercializadora del Sur',
                'address' => 'Carrera 70 #10-50',
                'city' => 'Cali',
                'department' => 'Valle del Cauca',
                'country' => 'Colombia',
                'phone' => '3123334455',
                'email' => 'info3@empresa.com',
                'tax_regime' => 'Común',
                'logo_url' => null,
                'ciiu_code' => '4700',
                'legal_representative_name' => 'Carlos López',
                'legal_representative_document_type' => 'NIT',
                'legal_representative_document_number' => '900321654',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_name' => 'Innovaciones del Norte',
                'nit' => '800999888-4',
                'trade_name' => 'Innovaciones',
                'address' => 'Calle 22 #5-15',
                'city' => 'Barranquilla',
                'department' => 'Atlántico',
                'country' => 'Colombia',
                'phone' => '3154445566',
                'email' => 'info4@empresa.com',
                'tax_regime' => 'Común',
                'logo_url' => null,
                'ciiu_code' => '6202',
                'legal_representative_name' => 'Ana Torres',
                'legal_representative_document_type' => 'CE',
                'legal_representative_document_number' => '123987456',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
