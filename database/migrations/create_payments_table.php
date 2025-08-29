<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id'); // identificación PK

            // Llaves foráneas
            $table->unsignedBigInteger('electronic_invoice_id'); // FK hacia factura electrónica
             $table->unsignedBigInteger('payment_method_id'); // FK hacia método de pago

            // Relaciones (foreign keys)
            $table->foreign('electronic_invoice_id')->references('id')->on('electronic_invoices')->onDelete('cascade');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('cascade');


            $table->date('fecha_pago');
            $table->decimal('valor_pagado', 15, 2);
            $table->string('moneda', 3);
            $table->string('referencia_pago', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
