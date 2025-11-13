<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Company;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Asigna company_id a los registros existentes que tienen company_id NULL
     */
    public function up(): void
    {
        // Obtener la primera empresa disponible
        $firstCompany = Company::first();
        
        if (!$firstCompany) {
            // Si no hay empresas, crear una por defecto
            $firstCompany = Company::create([
                'business_name' => 'Empresa Por Defecto',
                'nit' => '999999999-1',
                'trade_name' => 'Empresa Por Defecto',
                'address' => 'Dirección por defecto',
                'city' => 'Bogotá',
                'department' => 'Cundinamarca',
                'country' => 'Colombia',
                'phone' => '3000000000',
                'email' => 'default@empresa.com',
                'tax_regime' => 'Común',
            ]);
        }
        
        $companyId = $firstCompany->id;
        
        // Asignar company_id a productos sin empresa
        DB::table('products')
            ->whereNull('company_id')
            ->update(['company_id' => $companyId]);
        
        // Asignar company_id a servicios sin empresa
        DB::table('services')
            ->whereNull('company_id')
            ->update(['company_id' => $companyId]);
        
        // Asignar company_id a impuestos sin empresa
        DB::table('taxes')
            ->whereNull('company_id')
            ->update(['company_id' => $companyId]);
        
        // Asignar company_id a unidades de medida sin empresa
        DB::table('measurement_units')
            ->whereNull('company_id')
            ->update(['company_id' => $companyId]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No revertir porque no sabemos cuáles tenían NULL originalmente
        // Si necesitas revertir, puedes ejecutar manualmente:
        // DB::table('products')->where('company_id', $companyId)->update(['company_id' => null]);
    }
};
