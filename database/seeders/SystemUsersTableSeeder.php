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
                'nombre_completo' => 'Juan Pérez',
                'rol' => 'Admin',
                'contrasena' => Hash::make('AdminPass123'),
                'correo_electronico' => 'juan.perez@example.com',
                'telefono' => '3001234567',
                'estado' => true,
                'ultimo_acceso' => now(),
                'numero_identificacion' => '123456789'
            ],
            [
                'nombre_completo' => 'María Gómez',
                'rol' => 'Facturador',
                'contrasena' => Hash::make('Factura2025'),
                'correo_electronico' => 'maria.gomez@example.com',
                'telefono' => '3109876543',
                'estado' => true,
                'ultimo_acceso' => now()->subDays(1),
                'numero_identificacion' => '987654321'
            ],
            [
                'nombre_completo' => 'Carlos Ruiz',
                'rol' => 'Admin',
                'contrasena' => Hash::make('SeguraClave88'),
                'correo_electronico' => 'carlos.ruiz@example.com',
                'telefono' => '3155557890',
                'estado' => false,
                'ultimo_acceso' => null,
                'numero_identificacion' => '112233445'
            ],
            [
                'nombre_completo' => 'Laura Fernández',
                'rol' => 'Facturador',
                'contrasena' => Hash::make('ClaveFuerte99'),
                'correo_electronico' => 'laura.fernandez@example.com',
                'telefono' => '3204443322',
                'estado' => true,
                'ultimo_acceso' => now()->subHours(5),
                'numero_identificacion' => '556677889'
            ],
            [
                'nombre_completo' => 'Andrés López',
                'rol' => 'Admin',
                'contrasena' => Hash::make('AdminSuper2025'),
                'correo_electronico' => 'andres.lopez@example.com',
                'telefono' => null,
                'estado' => true,
                'ultimo_acceso' => now()->subDays(3),
                'numero_identificacion' => '998877665'
            ],
        ]);
    }
}

