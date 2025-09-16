<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\Role;
use Faker\Factory as Faker;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_CO'); // Usar Faker para datos de prueba

        $adminRole = Role::where('nombre', 'administrador')->first()->id;
        $facturadorRole = Role::where('nombre', 'facturador')->first()->id;
        $contadorRole = Role::where('nombre', 'contador')->first()->id;
        $clienteRole = Role::where('nombre', 'cliente')->first()->id;

        $companies = Company::all();

        foreach ($companies as $company) {
            // Admin, Facturador y Contador (valores de prueba consistentes)
            $users = [
                // 2 administradores
                [
                    'company_id' => $company->id, 'role_id' => $adminRole, 'nombre' => 'Admin 1 - ' . $company->razon_social,
                    'tipo_documento' => 'CC', 'numero_documento' => '1000' . $company->id . '001',
                    'correo_electronico' => 'admin1_' . $company->id . '@example.com', 'contrasena' => bcrypt('password'),
                    'direccion' => $faker->streetAddress, 'pais' => 'Colombia', 'telefono' => $faker->phoneNumber,
                    'descripcion' => 'Administrador principal', 'estado' => 'Activo',
                ],
                [
                    'company_id' => $company->id, 'role_id' => $adminRole, 'nombre' => 'Admin 2 - ' . $company->razon_social,
                    'tipo_documento' => 'CC', 'numero_documento' => '1000' . $company->id . '002',
                    'correo_electronico' => 'admin2_' . $company->id . '@example.com', 'contrasena' => bcrypt('password'),
                    'direccion' => $faker->streetAddress, 'pais' => 'Colombia', 'telefono' => $faker->phoneNumber,
                    'descripcion' => 'Administrador secundario', 'estado' => 'Activo',
                ],
                // 3 facturadores
                [
                    'company_id' => $company->id, 'role_id' => $facturadorRole, 'nombre' => 'Facturador 1 - ' . $company->razon_social,
                    'tipo_documento' => 'CC', 'numero_documento' => '2000' . $company->id . '001',
                    'correo_electronico' => 'facturador1_' . $company->id . '@example.com', 'contrasena' => bcrypt('password'),
                    'direccion' => $faker->streetAddress, 'pais' => 'Colombia', 'telefono' => $faker->phoneNumber,
                    'descripcion' => 'Encargado de facturación', 'estado' => 'Activo',
                ],
                [
                    'company_id' => $company->id, 'role_id' => $facturadorRole, 'nombre' => 'Facturador 2 - ' . $company->razon_social,
                    'tipo_documento' => 'CC', 'numero_documento' => '2000' . $company->id . '002',
                    'correo_electronico' => 'facturador2_' . $company->id . '@example.com', 'contrasena' => bcrypt('password'),
                    'direccion' => $faker->streetAddress, 'pais' => 'Colombia', 'telefono' => $faker->phoneNumber,
                    'descripcion' => 'Encargado de facturación', 'estado' => 'Activo',
                ],
                [
                    'company_id' => $company->id, 'role_id' => $facturadorRole, 'nombre' => 'Facturador 3 - ' . $company->razon_social,
                    'tipo_documento' => 'CC', 'numero_documento' => '2000' . $company->id . '003',
                    'correo_electronico' => 'facturador3_' . $company->id . '@example.com', 'contrasena' => bcrypt('password'),
                    'direccion' => $faker->streetAddress, 'pais' => 'Colombia', 'telefono' => $faker->phoneNumber,
                    'descripcion' => 'Encargado de facturación', 'estado' => 'Activo',
                ],
                // 1 contador
                [
                    'company_id' => $company->id, 'role_id' => $contadorRole, 'nombre' => 'Contador - ' . $company->razon_social,
                    'tipo_documento' => 'CC', 'numero_documento' => '3000' . $company->id . '001',
                    'correo_electronico' => 'contador_' . $company->id . '@example.com', 'contrasena' => bcrypt('password'),
                    'direccion' => $faker->streetAddress, 'pais' => 'Colombia', 'telefono' => $faker->phoneNumber,
                    'descripcion' => 'Encargado de contabilidad', 'estado' => 'Activo',
                ],
            ];

            // Insertar los usuarios con valores de prueba consistentes
            DB::table('users')->insert($users);

            // Clientes (10 por empresa, con datos aleatorios)
            for ($i = 1; $i <= 10; $i++) {
                DB::table('users')->insert([
                    'company_id' => $company->id,
                    'role_id' => $clienteRole,
                    'nombre' => $faker->name,
                    'tipo_documento' => $faker->randomElement(['CC', 'CE']),
                    'numero_documento' => $faker->unique()->numerify('##########'),
                    'correo_electronico' => $faker->unique()->safeEmail,
                    'contrasena' => bcrypt('password'),
                    'direccion' => $faker->streetAddress,
                    'pais' => 'Colombia',
                    'telefono' => $faker->phoneNumber,
                    'descripcion' => 'Usuario cliente',
                    'estado' => 'Activo',
                ]);
            }
        }
    }
}