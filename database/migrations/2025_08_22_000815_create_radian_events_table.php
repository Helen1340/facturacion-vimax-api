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

            //definicion de la fk de electronic document
            //$table->unsignedBigInteger('electronic_document_id'); // Bigint(FK) - Relación con ElectronicDocument
            //$table->foreign('electronic_document_id')->references('id')->on('electronic_documents')->onDelete('cascade');

            $table->id(); // Bigint(PK)
            $table->string('codigo', 20); // Varchar(20)
            $table->timestamp('fecha_evento'); // Timestamp
            $table->string('tipo_evento', 50); // Varchar(50)
            $table->text('xml_respuesta'); // Text
            $table->string('estado_dian', 50); // Varchar(50)

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
