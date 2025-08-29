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

        
            $table->string('motivo', 250); 
            $table->enum('tipo_documento', ['debito', 'credito']); // Enum para tipo de documento
            $table->string('descripcion', 250); // Varchar(250)
            $table->string('numero_nota', 50); // Varchar(50)
            $table->enum('estado', ['aceptada', 'rechazada', 'pendiente']); // Enum para estado
            $table->date('fecha_emision'); // Date
            $table->decimal('valor_total', 18, 2); // Decimal(18,2)

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
