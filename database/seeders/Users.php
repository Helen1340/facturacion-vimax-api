<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class Users extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::insert([
            [
                'company_id' => 1,
                'nombre' => 'Juan Pérez',
                'tipo_documento' => 'CC',
                'numero_documento' => '1002003001',
                'direccion' => 'Calle 123 #45-67',
                'pais' => 'Colombia',
                'descripcion' => 'Administrador del sistema',
                'contrasena' => Hash::make('12345678'),
                'correo_electronico' => 'juan.perez@example.com',
                'telefono' => '3001234567',
                'estado' => 'Activo',
                'ultimo_acceso' => now(),
            ],
            [
                'company_id' => 1,
                'nombre' => 'María Gómez',
                'tipo_documento' => 'CC',
                'numero_documento' => '1002003002',
                'direccion' => 'Carrera 10 #20-30',
                'pais' => 'Colombia',
                'descripcion' => 'Contadora general',
                'contrasena' => Hash::make('marita2025'),
                'correo_electronico' => 'maria.gomez@example.com',
                'telefono' => '3109876543',
                'estado' => 'Activo',
                'ultimo_acceso' => now(),
            ],
            [
                'company_id' => 2,
                'nombre' => 'Carlos Ramírez',
                'tipo_documento' => 'NIT',
                'numero_documento' => '90012345',
                'direccion' => 'Av. Siempre Viva #123',
                'pais' => 'México',
                'descripcion' => 'Soporte técnico',
                'contrasena' => Hash::make('carlos2025'),
                'correo_electronico' => 'carlos.ramirez@example.com',
                'telefono' => '3201239876',
                'estado' => 'Activo',
                'ultimo_acceso' => now(),
            ],
            [
                'company_id' => 2,
                'nombre' => 'Laura Fernández',
                'tipo_documento' => 'CC',
                'numero_documento' => '1056789123',
                'direccion' => 'Cra 50 #22-15',
                'pais' => 'Argentina',
                'descripcion' => 'Diseñadora gráfica',
                'contrasena' => Hash::make('laura2025'),
                'correo_electronico' => 'laura.fernandez@example.com',
                'telefono' => '3014567890',
                'estado' => 'Inactivo',
                'ultimo_acceso' => now(),
            ],
            [
                'company_id' => 3,
                'nombre' => 'Pedro López',
                'tipo_documento' => 'CC',
                'numero_documento' => '1100223344',
                'direccion' => 'Calle Principal #99',
                'pais' => 'Perú',
                'descripcion' => 'Vendedor externo',
                'contrasena' => Hash::make('pedrito2025'),
                'correo_electronico' => 'pedro.lopez@example.com',
                'telefono' => '3026547890',
                'estado' => 'Activo',
                'ultimo_acceso' => now(),
            ],
        ]);
    }
}
