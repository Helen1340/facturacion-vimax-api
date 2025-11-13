<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Company;
use App\Models\Product;
use App\Models\Service;
use App\Models\Tax;
use App\Models\MeasurementUnit;
use Illuminate\Support\Facades\DB;

class AssignRecordsToCompany extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'records:assign-company 
                            {--company-id= : ID de la empresa a asignar}
                            {--model=all : Modelo a asignar (products, services, taxes, measurement_units, all)}
                            {--from-company= : ID de la empresa origen (opcional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asigna registros (productos, servicios, impuestos, unidades) a una empresa específica';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $companyId = $this->option('company-id');
        $model = $this->option('model');
        $fromCompany = $this->option('from-company');

        if (!$companyId) {
            // Mostrar lista de empresas
            $companies = Company::all();
            if ($companies->isEmpty()) {
                $this->error('No hay empresas en el sistema');
                return 1;
            }

            $this->info('Empresas disponibles:');
            foreach ($companies as $company) {
                $this->line("  [{$company->id}] {$company->business_name} (NIT: {$company->nit})");
            }

            $companyId = $this->ask('Ingrese el ID de la empresa a asignar');
        }

        $company = Company::find($companyId);
        if (!$company) {
            $this->error("Empresa con ID {$companyId} no encontrada");
            return 1;
        }

        $this->info("Asignando registros a: {$company->business_name} (ID: {$company->id})");

        $updated = 0;

        if ($model === 'all' || $model === 'products') {
            $query = Product::query();
            if ($fromCompany) {
                $query->where('company_id', $fromCompany);
            } else {
                $query->whereNull('company_id');
            }
            $count = $query->update(['company_id' => $companyId]);
            $updated += $count;
            $this->info("  ✓ Productos asignados: {$count}");
        }

        if ($model === 'all' || $model === 'services') {
            $query = Service::query();
            if ($fromCompany) {
                $query->where('company_id', $fromCompany);
            } else {
                $query->whereNull('company_id');
            }
            $count = $query->update(['company_id' => $companyId]);
            $updated += $count;
            $this->info("  ✓ Servicios asignados: {$count}");
        }

        if ($model === 'all' || $model === 'taxes') {
            $query = Tax::query();
            if ($fromCompany) {
                $query->where('company_id', $fromCompany);
            } else {
                $query->whereNull('company_id');
            }
            $count = $query->update(['company_id' => $companyId]);
            $updated += $count;
            $this->info("  ✓ Impuestos asignados: {$count}");
        }

        if ($model === 'all' || $model === 'measurement_units') {
            $query = MeasurementUnit::query();
            if ($fromCompany) {
                $query->where('company_id', $fromCompany);
            } else {
                $query->whereNull('company_id');
            }
            $count = $query->update(['company_id' => $companyId]);
            $updated += $count;
            $this->info("  ✓ Unidades de medida asignadas: {$count}");
        }

        $this->info("\n✓ Total de registros asignados: {$updated}");
        return 0;
    }
}
