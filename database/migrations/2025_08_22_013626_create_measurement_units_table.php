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
            $table->bigIncrements('id'); // PK
            $table->string('nombre', 100);
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
            $table->string('codigo_dian', 10)->unique(); // Código único de la DIAN
            $table->text('descripcion')->nullable();
            $table->enum('tipo_aplicacion', ['Producto', 'Servicio']);

            $table->timestamps(); // created_at y updated_at
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
