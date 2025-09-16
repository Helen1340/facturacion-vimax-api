<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Service;
use App\Models\Tax;

class ServiceTaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todos los servicios que se han sembrado
        $services = Service::all();
        // Obtener todos los impuestos aplicables a servicios (IVA e INC)
        $serviceTaxes = Tax::whereIn('tipo', ['IVA', 'INC'])->get();

        foreach ($services as $service) {
            // Asignar un impuesto aleatorio de la lista de impuestos de servicio
            DB::table('service_tax')->insert([
                'service_id' => $service->id,
                'tax_id' => $serviceTaxes->random()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}