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
        Schema::create('product_services', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('CodigoProductoServicio')->unique();
            $table->decimal('CostoUnitario');
            $table->enum('Tipo', ['Producto', 'Servicio']);
            $table->string('Nombre');
            $table->text('Descripcion');
            $table->string('UsuarioCreacion');
            $table->decimal('PorcentajeIva');
            $table->boolean('AplicaImpuesto');
            $table->boolean('Estado');

            // llave foranea a la tabla de unidades de medida
            //$table->unsignedBigInteger('unit_of_measure_id')->nullable();


            //$table->foreign('unit_of_measure_id')->references('id')->on('unit_of_measures')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_services');
    }
};
