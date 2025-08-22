<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use App\Models\CreditDebitNote;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CreditDebitNoteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // usar factory para crear 50 registros de CreditDebitNote
        CreditDebitNote::factory()->count(50)->create();
    }
}
