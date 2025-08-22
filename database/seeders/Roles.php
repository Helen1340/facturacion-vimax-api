<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Roles extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::insert([
            [
                'nombre' => 'Administrador',
                'descripcion' => 'Acceso completo al sistema, puede gestionar todos los módulos y usuarios',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Facturador',
                'descripcion' => 'Acceso a la gestión de su área, reportes y supervisión de equipos',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Empleado',
                'descripcion' => 'Acceso básico al sistema para realizar tareas operativas diarias',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Contador',
                'descripcion' => 'Acceso a módulos financieros, contables y generación de reportes fiscales',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Cliente',
                'descripcion' => 'Acceso limitado de solo lectura para consultas básicas del sistema',
                'estado' => 'inactivo',
            ],
        ]);
    }
}
