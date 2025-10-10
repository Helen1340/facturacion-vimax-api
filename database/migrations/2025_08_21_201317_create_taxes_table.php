<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('tax_code', 50)->unique(); // Código del tributo, único por DIAN
            $table->string('name', 100); // Nombre del tributo
            $table->text('description')->nullable(); // Descripción del tributo (opcional)
            $table->string('type', 50); // Tipo de tributo: impuesto, retención, contribución, etc.
            $table->decimal('percentage', 5, 2)->nullable(); // Porcentaje aplicado sobre la base (opcional si es valor fijo)
            $table->decimal('fixed_value', 18, 2)->nullable(); // Valor fijo si aplica en lugar de porcentaje
            $table->enum('application_type', ['Porcentaje', 'ValorFijo', 'Retencion'])->default('Porcentaje'); 
            // Tipo de aplicación: porcentaje, valor fijo, retención
            $table->decimal('min_value', 18, 2)->nullable(); // Valor mínimo aplicable (opcional)
            $table->decimal('max_value', 18, 2)->nullable(); // Valor máximo aplicable (opcional)
            $table->enum('status', ['Activo', 'Inactivo'])->default('Activo'); // Estado del tributo
            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};
