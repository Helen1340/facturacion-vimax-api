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
            [
                'role_name' => 'administrator', // Administrador del sistema
                'description' => 'System administrator with full permissions', // Descripción del rol
                'status' => 'active', // Estado activo
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_name' => 'billing', // Facturador
                'description' => 'User authorized to create and send invoices', 
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_name' => 'accountant', // Contador
                'description' => 'User responsible for accounting and tax reports',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_name' => 'client', // Cliente
                'description' => 'Client who receives and reviews electronic documents',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
