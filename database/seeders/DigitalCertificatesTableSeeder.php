<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DigitalCertificatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('digital_certificates')->insert([
            [
                'NIT'               => 900123456,
                'Nombre_Certificado' => 'Certificado Tributario Anual 2025',
                'Ruta_Certificado'   => '/storage/certificados/tributario2025.pfx',
                'Contrasena'        => 'claveSegura123',
                'Fecha_Inicio'       => Carbon::create(2025, 1, 1),
                'Fecha_Fin'          => Carbon::create(2026, 1, 1),
                'Estado'            => 'Vigente',
                'Proveedor'         => 'Certicamara S.A.',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'NIT'               => 901234567,
                'Nombre_Certificado' => 'Certificado Firma Electrónica 2024',
                'Ruta_Certificado'   => '/storage/certificados/firma2024.pfx',
                'Contrasena'        => 'firmaSecure!24',
                'Fecha_Inicio'       => Carbon::create(2024, 5, 10),
                'Fecha_Fin'          => Carbon::create(2025, 5, 10),
                'Estado'            => 'Vigente',
                'Proveedor'         => 'GSE Software Ltda.',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'NIT'               => 902345678,
                'Nombre_Certificado' => 'Certificado Seguridad Web 2023',
                'Ruta_Certificado'   => '/storage/certificados/ssl2023.pfx',
                'Contrasena'        => 'sslKey!2023',
                'Fecha_Inicio'       => Carbon::create(2023, 7, 1),
                'Fecha_Fin'          => Carbon::create(2024, 7, 1),
                'Estado'            => 'Vencido',
                'Proveedor'         => 'GlobalSign',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'NIT'               => 903456789,
                'Nombre_Certificado' => 'Certificado Sello Digital Empresa',
                'Ruta_Certificado'   => '/storage/certificados/selloEmpresa.pfx',
                'Contrasena'        => 'sello2025Pass',
                'Fecha_Inicio'       => Carbon::create(2025, 3, 1),
                'Fecha_Fin'          => Carbon::create(2027, 3, 1),
                'Estado'            => 'Vigente',
                'Proveedor'         => 'Digicert Inc.',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'NIT'               => 904567890,
                'Nombre_Certificado' => 'Certificado Fiscal Revocado',
                'Ruta_Certificado'   => '/storage/certificados/fiscal_revocado.pfx',
                'Contrasena'        => 'revocado!321',
                'Fecha_Inicio'       => Carbon::create(2024, 1, 15),
                'Fecha_Fin'          => Carbon::create(2025, 1, 15),
                'Estado'            => 'Revocado',
                'Proveedor'         => 'Certicamara S.A.',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
        ]);
    }
}
