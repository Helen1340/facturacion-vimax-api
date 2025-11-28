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
        Schema::create('dian_numberings', function (Blueprint $table) {
            $table->id(); // Bigint(PK)
            // definicion de llave foranea para company
            $table->unsignedBigInteger('company_id'); // Bigint(FK) - Relación con Company
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            
           // Información de la numeración según DIAN
            $table->enum('document_type', ['Factura', 'NotaCredito', 'NotaDebito']); // Tipo de documento
            $table->string('document_type_code', 100)->nullable(); // Código del tipo de documento según DIAN (opcional)
            $table->string('prefix', 10); // Prefijo de numeración
            $table->unsignedBigInteger('start_number'); // Número inicial autorizado
            $table->unsignedBigInteger('end_number'); // Número final autorizado
            $table->date('resolution_date'); // Fecha de resolución DIAN
            $table->string('resolution_number', 50); // Número de resolución DIAN
            $table->date('validity_start_date'); // Fecha inicio de vigencia de la resolución
            $table->date('validity_end_date'); // Fecha fin de vigencia de la resolución
            $table->enum('current_status', ['Activo', 'Inactivo']); // Estado actual de la numeración
            $table->enum('environment', ['Pruebas', 'Producción'])->default('Pruebas'); // Ambiente de uso de la numeración
            $table->string('description', 255)->nullable(); // Descripción opcional para referencia interna
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dian_numberings');
    }
};
