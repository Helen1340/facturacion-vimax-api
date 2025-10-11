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
        Schema::create('credit_debit_notes', function (Blueprint $table) {
             $table->id(); // Bigint(PK)

            //definicion de llave foranea de electronic invoice(factura electronica)
            $table->unsignedBigInteger('electronic_invoice_id'); // Esta FK va en la tabla electronic_invoices
            $table->foreign('electronic_invoice_id')->references('id')->on('electronic_invoices')->onDelete('cascade');


            // Campos específicos de la nota
            $table->string('reason', 250); // Motivo de la nota crédito/débito
            $table->enum('note_type', ['debit', 'credit']); // Tipo de documento: débito o crédito
            $table->string('note_number', 50); // Número de la nota
            $table->enum('status', ['accepted', 'rejected', 'pending']); // Estado de la nota
            $table->date('issue_date'); // Fecha de emisión
            $table->decimal('total_amount', 18, 2); // Valor total de la nota

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_debit_notes');
    }
};
