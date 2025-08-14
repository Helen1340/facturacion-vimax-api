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
            $table->unsignedBigInteger('NIT'); // FK Bigint

            $table->string('Nombre_Certificado', 225);
            $table->text('Ruta_Certificado');
            $table->string('Contrasena', 225);
            $table->date('Fecha_Inicio');
            $table->date('Fecha_Fin');
            $table->enum('Estado', ['Vigente', 'Vencido', 'Revocado']);
            $table->string('Proveedor', 100);

            $table->timestamps();

            // Relación FK con la tabla correspondiente ( empresas)

            //$table->foreign('NIT')->references('NIT')->on('empresas')->onDelete('cascade');
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
