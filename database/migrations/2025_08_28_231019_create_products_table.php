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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // FK hacia MeasurementUnit
            $table->unsignedBigInteger('measurementunit_id');
            // Clave foránea
            //$table->foreign('measurementunit_id')->references('id')->on('measurement_units')->onDelete('cascade');

            $table->string('codigo_estandar', 50)->nullable();
            $table->string('codigo_producto', 50)->unique();
            $table->string('nombre', 150);
            $table->string('descripcion', 150)->nullable();
            $table->decimal('precio_unitario', 18, 6);
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');

            $table->timestamps();

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
