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
            $table->string('razon_social',150);
            $table->enum('tipo_documento', ['NIT', 'CC', 'CE']);
            $table->string('direccion', 150);
            $table->string('municipio', 100);
            $table->string('departamento', 100);
            $table->string('pais', 50);
            $table->string('telefono', 20);
            $table->string('correo_electronico', 100)->unique();
            $table->string('regimen', 50);
            $table->text('logo_url')->nullable();
            $table->string('nombre_comercial', 150)->nullable();
            $table->string('codigo_ciiu', 10)->nullable();
            $table->string('numero_documento', 20)->unique();
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
