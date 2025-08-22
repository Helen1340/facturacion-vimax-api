<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use App\Models\DianNumbering;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DianNumberingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // usar el factory para crear 50 registros de DianNumbering
        DianNumbering::factory()->count(50)->create();
    }
}
