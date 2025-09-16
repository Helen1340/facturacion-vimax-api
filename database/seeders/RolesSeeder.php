<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            ['nombre' => 'administrador', 'descripcion' => 'Administrador del sistema', 'estado' => 'activo'],
            ['nombre' => 'facturador', 'descripcion' => 'Usuario para emitir documentos', 'estado' => 'activo'],
            ['nombre' => 'contador', 'descripcion' => 'Usuario para reportes contables', 'estado' => 'activo'],
            ['nombre' => 'cliente', 'descripcion' => 'Usuario que recibe documentos', 'estado' => 'activo'],
        ]);
    }
}