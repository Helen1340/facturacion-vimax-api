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
    Schema::create('payments', function (Blueprint $table) {
        $table->id('IdPayments'); // PK

        $table->unsignedBigInteger('Numero_Factura'); // FK hacia facturas electrónicas

        $table->date('FechaPago');
        $table->decimal('ValorPagado');
        $table->string('Moneda');
        $table->string('MedioPago');

        // FK: Número de factura
        // $table->foreign('Numero_Factura')->references('NumeroFactura')->on('facturas_electronicas')->onDelete('cascade');

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
