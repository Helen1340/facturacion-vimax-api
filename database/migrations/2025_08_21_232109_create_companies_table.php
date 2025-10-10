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
           
            $table->string('business_name', 150); // Razón social de la empresa
            $table->string('nit', 50)->unique();  // NIT de la empresa (obligatorio)
            $table->string('trade_name', 150)->nullable(); // Nombre comercial de la empresa
            $table->string('address', 150); // Dirección física de la empresa
            $table->string('city', 100); // Ciudad donde se encuentra la empresa
            $table->string('department', 100); // Departamento donde se encuentra la empresa
            $table->string('country', 50); // País donde se encuentra la empresa
            $table->string('phone', 20);  // Teléfono de contacto de la empresa
            $table->string('email', 100)->unique();  // Correo electrónico de la empresa
            $table->string('tax_regime', 50); // Régimen tributario de la empresa
            $table->text('logo_url')->nullable(); // URL del logo de la empresa
            $table->string('ciiu_code', 10)->nullable(); // Código CIIU de la actividad económica principal
            $table->string('legal_representative_name', 150)->nullable(); // Nombre del representante legal
            // Tipo de documento del representante legal
            $table->enum('legal_representative_document_type', ['CC', 'CE', 'NIT', 'PAS'])->nullable();
            // Número de documento del representante legal
            $table->string('legal_representative_document_number', 20)->index()->nullable();

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
