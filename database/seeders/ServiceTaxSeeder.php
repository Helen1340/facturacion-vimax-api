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
        // 1. Obtener todos los servicios
        $services = Service::all();

        // 2. Obtener los impuestos aplicables (IVA e INC)
        $serviceTaxes = Tax::whereIn('type', ['IVA', 'INC'])->get();

        // 3. Validar que existan datos antes de continuar
        if ($services->isEmpty() || $serviceTaxes->isEmpty()) {
            echo "⚠️ No se encontraron servicios o impuestos aplicables (IVA/INC).\n";
            return;
        }

        // 4. Asignar un impuesto aleatorio a cada servicio
        foreach ($services as $service) {
            DB::table('service_tax')->insert([
                'service_id' => $service->id,
                'tax_id' => $serviceTaxes->random()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        echo "✅ Seeder de relación servicio-impuesto ejecutado correctamente.\n";
    }
}
