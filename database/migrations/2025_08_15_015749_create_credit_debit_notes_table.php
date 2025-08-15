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
            $table->unsignedBigInteger('IdUsuario');
            $table->string('Motivo');
            $table->enum('TipoNota', ['Crédito', 'Débito']);
            $table->text('Descripcion');
            $table->decimal('ValorTotal');
            $table->string('CufeNota');
            $table->string('XmlFormado');
            $table->enum('EstadoDian', ['Pendiente', 'Aprobado', 'Rechazado']);
            $table->date('FechaEmision');
            $table->char('Moneda');
            $table->timestamps();

            // Llave foránea (comentada por ahora)
            // $table->foreign('IdUsuario')->references('id')->on('system_users')->onDelete('cascade');
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
