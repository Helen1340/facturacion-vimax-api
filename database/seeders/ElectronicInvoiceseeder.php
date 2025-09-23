<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ElectronicInvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_CO');
        $companies = Company::all();

        foreach ($companies as $company) {
            $facturadorId = User::where('company_id', $company->id)
                                ->whereHas('role', function ($query) {
                                    $query->where('nombre', 'facturador');
                                })
                                ->inRandomOrder()
                                ->first()
                                ->id;

            for ($i = 1; $i <= 15; $i++) {
                DB::table('electronic_invoices')->insert([
                    'user_id' => $facturadorId,
                    'numero_factura' => "FE-{$company->id}-{$i}",
                    'fecha_emision' => now(),
                    'estado_interno' => $faker->randomElement(['emitida', 'borrador', 'anulada']),
                    'observacion' => $faker->sentence(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
