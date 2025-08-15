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
        Schema::create('invoice_details', function (Blueprint $table) {
            $table->id();
            $table->text('descripcion');
            $table->decimal('cantidad', 10, 2);
            $table->decimal('precio_unitario', 15, 2);
            $table->decimal('valor_total', 15, 2);
            $table->decimal('descuento', 15, 2)->nullable();
            $table->decimal('porcentaje_iva', 5, 2);
            $table->decimal('valor_iva', 15, 2);
            $table->string('unidad_medida', 20);
            $table->string('codigo_producto', 50);
            $table->text('observacion')->nullable();

            //$table->unsignedBigInteger('product_service_id')->nullable();
            //table->unsignedBigInteger('electronic_invoice_id')->nullable();

            //$table->foreign('product_service_id')->references('id')->on('product_services')->onDelete('set null');
            //$table->foreign('electronic_invoice_id')->references('id')->on('electronic_invoices')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_details');
    }
};
