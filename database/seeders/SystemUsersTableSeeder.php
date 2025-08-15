<?php

use Illuminate\Database\Seeder;
use App\Models\SystemUser;
use App\Models\SystemUsers;
use Illuminate\Support\Facades\Hash;

class SystemUserSeeder extends Seeder
{
    public function run()
    {
        SystemUsers::insert([
            [
                'IdUsuario' => 'USR001',
                'NombreCompleto' => 'Juan Pérez',
                'Rol' => 'Admin',
                'Contrasena' => Hash::make('AdminPass123'),
                'CorreoElectronico' => 'juan.perez@example.com',
                'Telefono' => '3001234567',
                'Estado' => true,
                'UltimoAcceso' => now(),
                'NumeroIdentificacion' => '123456789'
            ],
            [
                'IdUsuario' => 'USR002',
                'NombreCompleto' => 'María Gómez',
                'Rol' => 'Facturador',
                'Contrasena' => Hash::make('Factura2025'),
                'CorreoElectronico' => 'maria.gomez@example.com',
                'Telefono' => '3109876543',
                'Estado' => true,
                'UltimoAcceso' => now()->subDays(1),
                'NumeroIdentificacion' => '987654321'
            ],
            [
                'IdUsuario' => 'USR003',
                'NombreCompleto' => 'Carlos Ruiz',
                'Rol' => 'Admin',
                'Contrasena' => Hash::make('SeguraClave88'),
                'CorreoElectronico' => 'carlos.ruiz@example.com',
                'Telefono' => '3155557890',
                'Estado' => false,
                'UltimoAcceso' => null,
                'NumeroIdentificacion' => '112233445'
            ],
            [
                'IdUsuario' => 'USR004',
                'NombreCompleto' => 'Laura Fernández',
                'Rol' => 'Facturador',
                'Contrasena' => Hash::make('ClaveFuerte99'),
                'CorreoElectronico' => 'laura.fernandez@example.com',
                'Telefono' => '3204443322',
                'Estado' => true,
                'UltimoAcceso' => now()->subHours(5),
                'NumeroIdentificacion' => '556677889'
            ],
            [
                'IdUsuario' => 'USR005',
                'NombreCompleto' => 'Andrés López',
                'Rol' => 'Admin',
                'Contrasena' => Hash::make('AdminSuper2025'),
                'CorreoElectronico' => 'andres.lopez@example.com',
                'Telefono' => null,
                'Estado' => true,
                'UltimoAcceso' => now()->subDays(3),
                'NumeroIdentificacion' => '998877665'
            ],
        ]);
    }
}

