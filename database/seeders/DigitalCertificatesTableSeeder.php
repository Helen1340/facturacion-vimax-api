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
                'nit'               => 900123456,
                'nombre_certificado' => 'Certificado Tributario Anual 2025',
                'ruta_certificado'   => '/storage/certificados/tributario2025.pfx',
                'contrasena'        => 'claveSegura123',
                'fecha_inicio'       => Carbon::create(2025, 1, 1),
                'fecha_fin'          => Carbon::create(2026, 1, 1),
                'estado'            => 'Vigente',
                'proveedor'         => 'Certicamara S.A.',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'nit'               => 901234567,
                'nombre_certificado' => 'Certificado Firma Electrónica 2024',
                'ruta_certificado'   => '/storage/certificados/firma2024.pfx',
                'contrasena'        => 'firmaSecure!24',
                'fecha_inicio'       => Carbon::create(2024, 5, 10),
                'fecha_fin'          => Carbon::create(2025, 5, 10),
                'estado'            => 'Vigente',
                'proveedor'         => 'GSE Software Ltda.',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'nit'               => 902345678,
                'nombre_certificado' => 'Certificado Seguridad Web 2023',
                'ruta_certificado'   => '/storage/certificados/ssl2023.pfx',
                'contrasena'        => 'sslKey!2023',
                'fecha_inicio'       => Carbon::create(2023, 7, 1),
                'fecha_fin'          => Carbon::create(2024, 7, 1),
                'estado'            => 'Vencido',
                'proveedor'         => 'GlobalSign',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'nit'               => 903456789,
                'nombre_certificado' => 'Certificado Sello Digital Empresa',
                'ruta_certificado'   => '/storage/certificados/selloEmpresa.pfx',
                'contrasena'        => 'sello2025Pass',
                'fecha_inicio'       => Carbon::create(2025, 3, 1),
                'fecha_fin'          => Carbon::create(2027, 3, 1),
                'estado'            => 'Vigente',
                'proveedor'         => 'Digicert Inc.',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'nit'               => 904567890,
                'nombre_certificado' => 'Certificado Fiscal Revocado',
                'ruta_certificado'   => '/storage/certificados/fiscal_revocado.pfx',
                'contrasena'        => 'revocado!321',
                'fecha_inicio'       => Carbon::create(2024, 1, 15),
                'fecha_fin'          => Carbon::create(2025, 1, 15),
                'estado'            => 'Revocado',
                'proveedor'         => 'Certicamara S.A.',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
        ]);
    }
}
