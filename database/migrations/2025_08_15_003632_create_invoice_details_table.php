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
            $table->bigInteger('Id_DetalleFactura')->unique();
            $table->text('Descripcion');
            $table->decimal('Cantidad');
            $table->decimal('PrecioUnitario');
            $table->decimal('ValorTotal');
            $table->decimal('Descuento');
            $table->decimal('PorcentajeIVA');
            $table->decimal('ValorIVA');
            $table->string('UnidadMedida');
            $table->string('CodigoProducto');
            $table->text('Observacion')->nullable();

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
