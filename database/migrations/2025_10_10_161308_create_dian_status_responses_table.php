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
        Schema::create('dian_status_responses', function (Blueprint $table) {
            $table->id();
            // FK hacia el documento electrónico (relación con electronic_documents)
            $table->unsignedBigInteger('electronic_document_id');
            $table->foreign('electronic_document_id')
                  ->references('id')
                  ->on('electronic_documents')
                  ->onDelete('cascade');

            // Información de la respuesta (alineado con ApplicationResponse / RADIAN)
            $table->string('status_code', 20)->index();          // código de estado DIAN (ej: 200, 103, 114...)
            $table->string('status_description', 150);           // descripción corta
            $table->text('status_message')->nullable();          // mensaje detallado (texto libre)
            $table->text('response_xml')->nullable();            // XML completo recibido (si aplica)
            $table->string('protocol_number', 100)->nullable();  // opcional: número/protocolo devuelto por la DIAN
            $table->timestamp('received_at')->nullable()->index(); // fecha y hora en que se recibió la respuesta

            $table->timestamps(); // created_at, updated_at
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dian_status_responses');
    }
};
