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
        Schema::create('digital_certificates', function (Blueprint $table) {

            // relacion con la tabla company
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            $table->id();
            $table->string('certificate_name', 225); // Nombre del certificado digital
            $table->text('certificate_path');        // Ruta del archivo .p12/.pfx
            $table->string('serial_number', 100);    // Número serial emitido por la entidad certificadora
            $table->string('password', 150);         // Contraseña del archivo del certificado
            $table->date('start_date');              // Fecha de inicio de vigencia
            $table->date('end_date');                // Fecha de expiración
            $table->enum('status', ['Vigente', 'Vencido', 'Revocado']); // Estado actual
            $table->string('issuer', 100);           // Entidad emisora
            $table->enum('certificate_type', ['Producción', 'Pruebas'])->default('Pruebas'); // Tipo de certificado
            $table->string('signature_algorithm', 50)->nullable(); // Algoritmo de firma (SHA256withRSA)
            $table->string('uuid', 100)->nullable(); // Identificador único externo (opcional)
            $table->string('signature_type', 100)->nullable(); // tipo de firma
            $table->text('description')->nullable(); // Descripción opcional
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_certificates');
    }
};
