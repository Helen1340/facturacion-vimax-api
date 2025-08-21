<?php

namespace Database\Seeders;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DigitalCertificateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('digital_certificates')->insert([
            // Certificado 1: Vigente
            [
                'nombre_certificado' => 'Certificado SSL Empresa A',
                'ruta_certificado'   => '/certs/empresaA_ssl_2024.pem',
                'numero_serial'      => 'ABC123XYZ4567890',
                'contrasena'         => 'passSecure123',
                'fecha_inicio'       => '2024-01-01',
                'fecha_fin'          => '2025-01-01',
                'estado'             => 'Vigente',
                'entidad_emisora'    => 'Let\'s Encrypt',
            ],
            // Certificado 2: Vencido
            [
                'nombre_certificado' => 'Firma Digital Empresa B',
                'ruta_certificado'   => '/certs/empresaB_firma_2023.pfx',
                'numero_serial'      => 'DEF456UVW7890123',
                'contrasena'         => 'secretPassABC',
                'fecha_inicio'       => '2022-03-15',
                'fecha_fin'          => '2023-03-15',
                'estado'             => 'Vencido',
                'entidad_emisora'    => 'CertiSign',
            ],
            // Certificado 3: Revocado
            [
                'nombre_certificado' => 'Certificado de Autenticación Empresa A',
                'ruta_certificado'   => '/certs/empresaA_auth_revocado.cer',
                'numero_serial'      => 'GHI789JKL0123456',
                'contrasena'         => 'mySecureKey',
                'fecha_inicio'       => '2023-07-01',
                'fecha_fin'          => '2024-07-01',
                'estado'             => 'Revocado',
                'entidad_emisora'    => 'DigiCert',
            ],
            // Certificado 4: Vigente, otra compañía
            [
                'nombre_certificado' => 'Certificado SSL Tienda Online',
                'ruta_certificado'   => '/certs/tienda_online_ssl.pem',
                'numero_serial'      => 'MNO012PQR3456789',
                'contrasena'         => 'secureWebStore',
                'fecha_inicio'       => '2024-05-20',
                'fecha_fin'          => '2025-05-20',
                'estado'             => 'Vigente',
                'entidad_emisora'    => 'Comodo SSL',
            ],
            // Certificado 5: Nuevo registro de ejemplo
            [
                'nombre_certificado' => 'Certificado VPN Corporativo',
                'ruta_certificado'   => '/certs/corp_vpn_cert.p12',
                'numero_serial'      => 'PQR345STU6789012',
                'contrasena'         => 'VpnSafe2025',
                'fecha_inicio'       => '2025-01-01',
                'fecha_fin'          => '2026-01-01',
                'estado'             => 'Vigente',
                'entidad_emisora'    => 'Internal CA',
            ],
        ]);
    }
}
