<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class Users extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    // Generar 50 usuarios aleatorios con la factory
    User::factory()->count(50)->create();
    }
}
