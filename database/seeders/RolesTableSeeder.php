<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'nombre' => 'Administrador',
                'descripcion' => 'Acceso completo al sistema, puede gestionar usuarios, roles, permisos, facturas y configuraciones.'
            ],
            [
                'nombre' => 'Facturador',
                'descripcion' => 'Puede crear y gestionar facturas, ver clientes y productos, pero no puede modificar roles ni configuraciones.'
            ],
            [
                'nombre' => 'Contador',
                'descripcion' => 'Acceso a reportes y consultas financieras, sin permisos para crear facturas o gestionar usuarios.'
            ],
        ];
        
        // Recorre los roles y crea o actualiza cada uno evitando duplicados
        foreach ($roles as $role) {
            Role::updateOrCreate(['nombre' => $role['nombre']], $role);
        }
    
    }
}
