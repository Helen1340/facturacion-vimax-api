<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class permissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('permissions')->insert([
            [
                'Nombre' => 'Crear Usuario',
                'Descripcion' => 'Permite crear nuevos usuarios en el sistema',
            ],
            [
                'Nombre' => 'Editar Usuario',
                'Descripcion' => 'Permite editar la información de los usuarios existentes',
            ],
            [
                'Nombre' => 'Eliminar Usuario',
                'Descripcion' => 'Permite eliminar usuarios del sistema',
            ],
            [
                'Nombre' => 'Ver Reportes',
                'Descripcion' => 'Permite visualizar los reportes generados',
            ],
            [
                'Nombre' => 'Gestionar Roles',
                'Descripcion' => 'Permite asignar y modificar roles de los usuarios',
            ],
        ]);
    }
}

