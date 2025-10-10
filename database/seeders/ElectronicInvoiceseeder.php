<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ElectronicInvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_CO');
        $companies = Company::all();

        foreach ($companies as $company) {
            // Buscar un usuario con rol 'billing' (facturador)
            $facturador = User::where('company_id', $company->id)
                ->whereHas('role', function ($query) {
                    $query->where('role_name', 'billing'); // 🔧 corregido aquí
                })
                ->inRandomOrder()
                ->first();

            // Si no hay facturador, saltar la empresa
            if (!$facturador) {
                continue;
            }

            // Crear 15 facturas electrónicas simuladas
            for ($i = 1; $i <= 15; $i++) {
                $subtotal = $faker->randomFloat(2, 50000, 500000);
                $impuesto = $subtotal * 0.19;
                $total = $subtotal + $impuesto;

                DB::table('electronic_invoices')->insert([
                    'user_id' => $facturador->id,

                    // Información principal
                    'invoice_number' => "FE-" . strtoupper(Str::random(4)) . "-{$company->id}-{$i}",
                    'issue_date' => now()->subDays(rand(0, 30)),
                    'internal_status' => $faker->randomElement(['draft', 'issued', 'cancelled']),
                    'observation' => $faker->sentence(8),

                    // Información UBL / DIAN
                    'ubl_version' => '2.1',
                    'customization_id' => 'DIAN 2.1: Factura Electrónica de Venta',
                    'profile_id' => 'DIAN 2.1',
                    'uuid' => Str::uuid()->toString(),
                    'document_currency_code' => 'COP',
                    'invoice_type_code' => '01',

                    // Totales principales
                    'line_extension_amount' => $subtotal,
                    'tax_exclusive_amount' => $subtotal,
                    'tax_inclusive_amount' => $total,
                    'payable_amount' => $total,

                    // Información de pago
                    
                    'payment_means_code' => '10',
                    'payment_means_name' => 'Contado',

                    // Estado ante la DIAN
                    'dian_status' => $faker->randomElement(['pending', 'sent', 'accepted', 'rejected', 'error', 'cancelled']),
                    'sent_at' => $faker->optional()->dateTimeBetween('-10 days', 'now'),
                    'received_at' => $faker->optional()->dateTimeBetween('-10 days', 'now'),

                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
