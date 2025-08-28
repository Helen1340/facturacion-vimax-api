<?php

namespace Database\Seeders;

use App\Models\ElectronicDocument;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ElectronicDocumentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    // Generar 50 documentos electrónicos aleatorios con la factory
    ElectronicDocument::factory()->count(50)->create();
    }
}
