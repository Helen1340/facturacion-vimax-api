<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Eliminar duplicados en product_tax antes de agregar el índice único
        $duplicates = DB::table('product_tax')
            ->select('product_id', 'tax_id', DB::raw('COUNT(*) as count'))
            ->groupBy('product_id', 'tax_id')
            ->having('count', '>', 1)
            ->get();

        foreach ($duplicates as $duplicate) {
            // Mantener solo el primer registro, eliminar los demás
            $ids = DB::table('product_tax')
                ->where('product_id', $duplicate->product_id)
                ->where('tax_id', $duplicate->tax_id)
                ->orderBy('id')
                ->skip(1) // Saltar el primero
                ->pluck('id');

            if ($ids->isNotEmpty()) {
                DB::table('product_tax')->whereIn('id', $ids)->delete();
            }
        }

        // Eliminar duplicados en service_tax antes de agregar el índice único
        $duplicates = DB::table('service_tax')
            ->select('service_id', 'tax_id', DB::raw('COUNT(*) as count'))
            ->groupBy('service_id', 'tax_id')
            ->having('count', '>', 1)
            ->get();

        foreach ($duplicates as $duplicate) {
            // Mantener solo el primer registro, eliminar los demás
            $ids = DB::table('service_tax')
                ->where('service_id', $duplicate->service_id)
                ->where('tax_id', $duplicate->tax_id)
                ->orderBy('id')
                ->skip(1) // Saltar el primero
                ->pluck('id');

            if ($ids->isNotEmpty()) {
                DB::table('service_tax')->whereIn('id', $ids)->delete();
            }
        }

        // Agregar índice único a product_tax
        try {
            Schema::table('product_tax', function (Blueprint $table) {
                $table->unique(['product_id', 'tax_id'], 'product_tax_product_id_tax_id_unique');
            });
        } catch (\Exception $e) {
            // El índice ya existe, continuar
        }

        // Agregar índice único a service_tax
        try {
            Schema::table('service_tax', function (Blueprint $table) {
                $table->unique(['service_id', 'tax_id'], 'service_tax_service_id_tax_id_unique');
            });
        } catch (\Exception $e) {
            // El índice ya existe, continuar
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_tax', function (Blueprint $table) {
            $table->dropUnique('product_tax_product_id_tax_id_unique');
        });

        Schema::table('service_tax', function (Blueprint $table) {
            $table->dropUnique('service_tax_service_id_tax_id_unique');
        });
    }
};
