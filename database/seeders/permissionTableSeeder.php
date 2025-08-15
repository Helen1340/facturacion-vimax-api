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
                'nombre' => 'Crear Usuario',
                'descripcion' => 'Permite crear nuevos usuarios en el sistema',
            ],
            [
                'bombre' => 'Editar Usuario',
                'descripcion' => 'Permite editar la información de los usuarios existentes',
            ],
            [
                'nombre' => 'Eliminar Usuario',
                'descripcion' => 'Permite eliminar usuarios del sistema',
            ],
            [
                'nombre' => 'Ver Reportes',
                'descripcion' => 'Permite visualizar los reportes generados',
            ],
            [
                'nombre' => 'Gestionar Roles',
                'descripcion' => 'Permite asignar y modificar roles de los usuarios',
            ],
        ]);
    }
}

