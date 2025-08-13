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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('nit',30)->unique();
            $table->enum('tipo_documento', ['NIT', 'CC', 'CE', 'TI']);
            $table->string('razon_social');
            $table->string('direccion', 150)->nullable();
            $table->string('municipio', 100)->nullable();
            $table->string('departamento', 100)->nullable();
            $table->string('pais', 50)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('correo_electronico', 100)->nullable();
            $table->string('regimen', 50)->nullable();
            $table->string('logo', 100)->nullable();
            $table->string('codigo_ciiu', 10)->nullable();
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
