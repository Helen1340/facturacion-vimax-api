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
        $table->id();
        $table->date('fecha_pago')->nullable();
        $table->decimal('valor_pagado', 15, 2);
        $table->string('moneda', 3);
        $table->string('medio_pago', 50);

    // FK: factura electrónica
        //$table->unsignedBigInteger('electronic_invioce_id')->nullable();
        // $table->foreign('electronic_invioce_id')->references('id')->on('electronic_invioce')->onDelete('cascade');

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
