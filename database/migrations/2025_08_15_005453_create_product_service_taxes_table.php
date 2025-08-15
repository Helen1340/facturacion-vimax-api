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
        Schema::create('product_service_taxes', function (Blueprint $table) {
            $table->id();
            $table->decimal('Porcentaje', 5, 2);
            $table->string('BaseImponible', 50);
            $table->enum('TipoAplicacion', ['traslado', 'retenido']);

            //$table->unsignedBigInteger('tax_id')->nullable(); // Llave foránea para la tabla de impuestos
            //$table->unsignedBigInteger('product_service_id')->nullable(); // Llave foránea para la tabla de productos y servicios
            
            //$table->foreign('tax_id')->references('id')->on('taxes')->onDelete('cascade');
            //$table->foreign('product_service_id')->references('id')->on('product_services')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_service_taxes');
    }
};
