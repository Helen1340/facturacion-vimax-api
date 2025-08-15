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
            $table->id();

            //$table->string('IdCertificado'); // PK Bigint 
            $table->unsignedBigInteger('nit'); // FK Bigint
            $table->string('nombre_certificado', 225);
            $table->text('ruta_certificado');
            $table->string('contrasena', 225);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->enum('estado', ['vigente', 'vencido', 'revocado']);
            $table->string('proveedor', 100);
            $table->timestamps();

            // Relación FK con la tabla correspondiente ( empresas)
            //$table->foreign('nit')->references('nit')->on('empresas')->onDelete('cascade');
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
