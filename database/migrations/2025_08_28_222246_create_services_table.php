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
        Schema::create('services', function (Blueprint $table) {
            $table->id();


            $table->unsignedBigInteger('measurement_unit_id');
            // Restricción de clave foránea
            $table->foreign('measurement_unit_id')->references('id')->on('measurement_units')->onDelete('cascade');

            $table->string('service_code', 50)->unique(); // código interno del servicio
            $table->string('name', 150); // nombre del servicio
            $table->string('description', 150)->nullable(); // descripción del servicio
            $table->decimal('unit_price', 18, 6); // precio unitario
            $table->enum('status', ['Active', 'Inactive'])->default('Active'); // estado
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
