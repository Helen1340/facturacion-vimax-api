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
        Schema::create('CreditDebitNote', function (Blueprint $table) {
            $table->id();
            $table->string('numero_nota')->unique();
            $table->string('Motivo', 255)->nullable();
            $table->enum('TipoNota', ['debito', 'credito']);
            $table->text('Descripcion')->nullable();
            $table->decimal('ValorTotal', 15, 2);
            $table->string('CUFENota', 100)->nullable();
            $table->text('XML_firmado')->nullable();
            $table->enum('EstadoDian', ['aceptada', 'rechazada', 'pendiente']);
            $table->date('FechaEmision');
            $table->string('Moneda', 3);
            $table->timestamps();

            // Llave foránea (comentada por ahora)
            //$table->unsignedBigInteger('system_user_id')->nullable();
            // $table->foreign('system_user_id')->references('id')->on('system_users')->onDelete('cascade');
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
