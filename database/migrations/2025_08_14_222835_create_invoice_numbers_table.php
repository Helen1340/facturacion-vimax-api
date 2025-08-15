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
        Schema::create('invoice_numbers', function (Blueprint $table) {
            $table->id();

            //$table->string('id_certificado', 10)->unique();
            $table->unsignedBigInteger('nit');
            $table->enum('tipo_documento', ['Factura', 'NotaCredito']);
            $table->string('prefijo', 10);
            $table->bigInteger('numero_inicial');
            $table->bigInteger('numero_final');
            $table->date('fecha_resolucion');
            $table->string('numero_resolucion', 50);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->boolean('estado_actual')->default(true);
            $table->timestamps();

            //$table->foreign('nit')->references('id')->on('clientes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_numbers');
    }
};
