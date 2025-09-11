<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // recomiendo mantener los roles fijos. Así aseguras que siempre existan los 4 roles principales
        $rolesFijos = [
            [
                'nombre' => 'administrador',
                'descripcion' => 'Acceso total al sistema',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'facturador',
                'descripcion' => 'Genera facturas',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'contador',
                'descripcion' => 'Gestiona la contabilidad',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'cliente',
                'descripcion' => 'Acceso como cliente',
                'estado' => 'activo',
            ],
        ];

        foreach ($rolesFijos as $rol) {
            Role::create($rol);
        }

        
    }
}
