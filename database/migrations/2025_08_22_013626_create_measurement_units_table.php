<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeasurementUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('measurement_units', function (Blueprint $table) {

            $table->id(); // Identificador
            $table->string('name', 100); // Nombre
            $table->enum('status', ['Active', 'Inactive'])->default('Active'); // Estado
            $table->string('dian_code', 10)->unique(); // Código DIAN
            $table->text('description')->nullable(); // Descripción
            $table->enum('application_type', ['Product', 'Service']); // Tipo de aplicación
            $table->timestamps(); // Fechas de creación y actualización

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('measurement_units');
    }
}
