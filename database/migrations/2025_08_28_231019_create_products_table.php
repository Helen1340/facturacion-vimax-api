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
            $table->unsignedBigInteger('measurement_unit_id');
            // Clave foránea
            $table->foreign('measurement_unit_id')->references('id')->on('measurement_units')->onDelete('cascade');

            $table->string('standard_code', 50)->nullable(); // código estándar (UNECE / GTIN)
            $table->string('product_code', 50)->unique(); // código interno del producto
            $table->string('name', 150); // nombre del producto
            $table->string('description', 150)->nullable(); // descripción del producto
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
        Schema::dropIfExists('products');
    }
};
