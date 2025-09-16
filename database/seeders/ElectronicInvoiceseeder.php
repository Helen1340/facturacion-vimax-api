<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\User;
use Faker\Factory as Faker;

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
                                })->inRandomOrder()->first()->id;

            // Este campo no existe en tu tabla electronic_invoices, así que lo eliminamos.
            // $clienteId = User::where('company_id', $company->id)
            //                  ->whereHas('role', function ($query) {
            //                      $query->where('nombre', 'cliente');
            //                  })->inRandomOrder()->first()->id;

            for ($i = 1; $i <= 15; $i++) {
                $subTotal = $faker->randomFloat(2, 50000, 500000);
                $impuesto = $subTotal * 0.19;
                $totalFactura = $subTotal + $impuesto;

                DB::table('electronic_invoices')->insert([
                    'user_id' => $facturadorId,
                    // 'client_id' => $clienteId, // <-- LÍNEA ELIMINADA
                    'numero_factura' => "FE-{$company->id}-{$i}",
                    'fecha_emision' => now(),
                    'sub_total' => $subTotal,
                    'total_impuesto' => $impuesto,
                    'total_factura' => $totalFactura,
                    'estado_interno' => $faker->randomElement(['Emitida', 'borrador', 'anulada']),
                    'descuento_total' => 0.00,
                    'observacion' => $faker->sentence(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}