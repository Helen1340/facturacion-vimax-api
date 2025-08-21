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

            // definicion de llave foranea para company
            //$table->unsignedBigInteger('company_id'); // Bigint(FK) - Relación con Company
            //$table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            $table->id(); // Bigint(PK)
            $table->enum('tipo_documento', ['Factura', 'NotaCredito', 'NotaDebito']); // Enum para tipos de documento
            $table->string('prefijo', 10); // Varchar(10)
            $table->bigInteger('numero_inicio'); // Bigint
            $table->bigInteger('numero_fin'); // Bigint
            $table->date('fecha_resolucion'); // Date
            $table->string('numero_resolucion', 50); // Varchar(50)
            $table->date('fecha_inicio'); // Date
            $table->date('fecha_fin'); // Date
            $table->enum('estado_actual', ['Activo', 'Inactivo']); // Enum para estado actual

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
