<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\Company; 

class CompanyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usa el factory para crear 50 registros de la compañía
        Company::factory()->count(50)->create();
    
    }
}
