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
            $table->string('nombre_certificado', 225);
            $table->text('ruta_certificado');
            $table->string('numero_serial', 100);
            $table->string('contrasena', 150);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->enum('estado', ['Vigente', 'Vencido', 'Revocado']);
            $table->string('entidad_emisora', 100);

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
