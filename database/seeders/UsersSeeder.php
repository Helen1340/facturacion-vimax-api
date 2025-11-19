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
        $faker = Faker::create('es_CO');

        // Buscar roles según el nuevo nombre en inglés (role_name)
        $adminRole = Role::where('role_name', 'administrador')->first()?->id;
        $billingRole = Role::where('role_name', 'Facturador')->first()?->id;
        $accountantRole = Role::where('role_name', 'Contador')->first()?->id;
        $clientRole = Role::where('role_name', 'cliente')->first()?->id;

        $companies = Company::all();

        foreach ($companies as $company) {

            // Usuarios internos: administradores, facturadores, contadores
            $users = [
                // 2 administradores
                [
                    'company_id' => $company->id,
                    'role_id' => $adminRole,
                    'first_name' => 'Admin 1 - ' . $company->business_name,
                    'document_type' => 'CC',
                    'document_number' => '1000' . $company->id . '001',
                    'email' => 'admin1_' . $company->id . '@example.com',
                    'password' => bcrypt('password'),
                    'address' => $faker->streetAddress,
                    'country' => 'Colombia',
                    'phone' => $faker->phoneNumber,
                    'description' => 'System administrator',
                    'status' => 'Active',
                    'last_access' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'company_id' => $company->id,
                    'role_id' => $adminRole,
                    'first_name' => 'Admin 2 - ' . $company->business_name,
                    'document_type' => 'CC',
                    'document_number' => '1000' . $company->id . '002',
                    'email' => 'admin2_' . $company->id . '@example.com',
                    'password' => bcrypt('password'),
                    'address' => $faker->streetAddress,
                    'country' => 'Colombia',
                    'phone' => $faker->phoneNumber,
                    'description' => 'System administrator',
                    'status' => 'Active',
                    'last_access' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                // 3 facturadores
                [
                    'company_id' => $company->id,
                    'role_id' => $billingRole,
                    'first_name' => 'Billing 1 - ' . $company->business_name,
                    'document_type' => 'CC',
                    'document_number' => '2000' . $company->id . '001',
                    'email' => 'billing1_' . $company->id . '@example.com',
                    'password' => bcrypt('password'),
                    'address' => $faker->streetAddress,
                    'country' => 'Colombia',
                    'phone' => $faker->phoneNumber,
                    'description' => 'Invoice manager',
                    'status' => 'Active',
                    'last_access' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'company_id' => $company->id,
                    'role_id' => $billingRole,
                    'first_name' => 'Billing 2 - ' . $company->business_name,
                    'document_type' => 'CC',
                    'document_number' => '2000' . $company->id . '002',
                    'email' => 'billing2_' . $company->id . '@example.com',
                    'password' => bcrypt('password'),
                    'address' => $faker->streetAddress,
                    'country' => 'Colombia',
                    'phone' => $faker->phoneNumber,
                    'description' => 'Invoice manager',
                    'status' => 'Active',
                    'last_access' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'company_id' => $company->id,
                    'role_id' => $billingRole,
                    'first_name' => 'Billing 3 - ' . $company->business_name,
                    'document_type' => 'CC',
                    'document_number' => '2000' . $company->id . '003',
                    'email' => 'billing3_' . $company->id . '@example.com',
                    'password' => bcrypt('password'),
                    'address' => $faker->streetAddress,
                    'country' => 'Colombia',
                    'phone' => $faker->phoneNumber,
                    'description' => 'Invoice manager',
                    'status' => 'Active',
                    'last_access' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                // 1 contador
                [
                    'company_id' => $company->id,
                    'role_id' => $accountantRole,
                    'first_name' => 'Accountant - ' . $company->business_name,
                    'document_type' => 'CC',
                    'document_number' => '3000' . $company->id . '001',
                    'email' => 'accountant_' . $company->id . '@example.com',
                    'password' => bcrypt('password'),
                    'address' => $faker->streetAddress,
                    'country' => 'Colombia',
                    'phone' => $faker->phoneNumber,
                    'description' => 'Responsible for accounting and reports',
                    'status' => 'Active',
                    'last_access' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            DB::table('users')->insert($users);

            // 10 clientes por empresa
            for ($i = 1; $i <= 10; $i++) {
                DB::table('users')->insert([
                    'company_id' => $company->id,
                    'role_id' => $clientRole,
                    'first_name' => $faker->name,
                    'document_type' => $faker->randomElement(['CC', 'CE']),
                    'document_number' => $faker->unique()->numerify('##########'),
                    'email' => $faker->unique()->safeEmail,
                    'password' => bcrypt('password'),
                    'address' => $faker->streetAddress,
                    'country' => 'Colombia',
                    'phone' => $faker->phoneNumber,
                    'description' => 'Client user',
                    'status' => 'Active',
                    'last_access' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
