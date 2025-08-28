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
        Schema::create('electronic_documents', function (Blueprint $table) {
            $table->id();

            // Llaves foráneas
            $table->unsignedBigInteger('ElectronicInvoice_id')->nullable();
            //$table->foreignId('ElectronicInvoice_id')->nullable()->constrained('electronic_invoices')->onDelete('cascade');
            $table->unsignedBigInteger('DianNumbering_id')->nullable();
            //$table->foreignId('DianNumbering_id')->nullable()->constrained('dian_numberings')->onDelete('cascade');
            $table->unsignedBigInteger('CreditDebitNote_id')->nullable();
            //$table->foreignId('CreditDebitNote_id')->nullable()->constrained('credit_debit_notes')->onDelete('cascade');

            $table->string('cufe', 255)->unique();
            $table->string('cude', 255);
            $table->longText('xml_documento');
            $table->string('estado_dian', 50);
            $table->date('fecha_validacion');
            $table->string('firma_digital', 50);
            $table->string('hash_documento', 150);
            $table->string('descripcion', 250);
            $table->enum('ambiente', ['Pruebas', 'Producción'])->default('Pruebas');
            $table->string('tipo_documento', 50);
            $table->longText('qr_codigo');
            $table->longText('cdr');
            $table->enum('modo_emision', ['normal', 'en contingencia'])->default('normal');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electronic_documents');
    }
};
