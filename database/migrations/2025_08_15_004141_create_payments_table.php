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

        $table->date('FechaPago')->nullable();
        $table->decimal('ValorPagado', 15, 2);
        $table->string('Moneda', 3);
        $table->string('MedioPago', 50);

    // FK: Número de factura

        //$table->unsignedBigInteger('Numero_Factura')->nullable();

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
