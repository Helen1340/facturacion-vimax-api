<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use App\Models\RadianEvent;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RadianEventTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // usar factory para crear 50 eventos radian
        RadianEvent::factory()->count(50)->create();
    }
}
