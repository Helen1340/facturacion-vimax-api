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
            $table->bigInteger('codigo_producto_servicio')->unique();
            $table->decimal('costo_unitario');
            $table->enum('tipo', ['Producto', 'Servicio']);
            $table->string('nombre');
            $table->text('descripcion');
            $table->string('usuario_creacion');
            $table->decimal('porcentaje_iva');
            $table->boolean('aplica_impuesto');
            $table->boolean('estado');

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
