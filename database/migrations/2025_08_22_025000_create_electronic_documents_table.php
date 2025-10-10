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
        
            $table->foreignId('electronic_invoice_id')->constrained('electronic_invoices');
            
            $table->foreignId('dian_numbering_id')->nullable()->constrained('dian_numberings')->onDelete('cascade');
            
            $table->foreignId('credit_debit_note_id')->nullable()->constrained('credit_debit_notes')->onDelete('cascade');

            // Identifiers / Identificadores únicos
            $table->string('cufe', 255)->unique(); // Código Único de Factura Electrónica (CUFE)
            $table->string('cude', 255); // Código Único de Documento Electrónico (CUDE)
            $table->longText('xml_document'); // XML del documento electrónico
            $table->string('dian_status', 50); // Estado del documento según la DIAN
            $table->date('validation_date'); // Fecha de validación del documento
            $table->string('digital_signature', 50); // Firma digital del documento
            $table->string('document_hash', 150); // Hash del documento electrónico
            $table->string('description', 250); // Descripción del documento
            $table->enum('environment', ['Pruebas', 'Producción'])->default('Pruebas'); // Ambiente de emisión: Pruebas o Producción
            $table->string('document_type', 50); // Tipo de documento (Factura, Nota Crédito, Nota Débito, etc.)
            $table->longText('qr_code'); // Código QR del documento
            $table->longText('cdr'); // Código de Respuesta de la DIAN
            $table->enum('emission_mode', ['normal', 'en contingencia'])->default('normal'); // Modo de emisión del documento

            $table->timestamps(); // created_at y updated_at
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
