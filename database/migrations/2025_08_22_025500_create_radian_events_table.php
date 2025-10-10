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
        Schema::create('radian_events', function (Blueprint $table) {
            $table->id(); // Bigint(PK)
            //definicion de la fk de electronic document
            $table->unsignedBigInteger('electronic_document_id'); // Bigint(FK) - Relación con ElectronicDocument
            $table->foreign('electronic_document_id')->references('id')->on('electronic_documents')->onDelete('cascade');

            $table->string('event_code', 10); // Código del evento RADIAN
            $table->string('event_name', 100); // Nombre o tipo del evento
            $table->timestamp('event_date'); // Fecha y hora del evento
            $table->string('event_uuid', 64); // Identificador único del evento (CUFE/UUID)
            $table->text('response_xml'); // XML de respuesta de la DIAN
            $table->enum('dian_status', ['pending','accepted','rejected','error','cancelled'])->default('pending');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('radian_events');
    }
};
