<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        //User::factory()->create([
          //  'name' => 'Test User',
          //  'email' => 'test@example.com',//

           // Llamas al seeder de companies
        $this->call(CompaniesTableSeeder::class);

        // Aquí luego podrás llamar a los seeders de tus otras tablas
        // $this->call(UsersTableSeeder::class);
        // $this->call(RolesTableSeeder::class);
        // etc.
    
    }
}
