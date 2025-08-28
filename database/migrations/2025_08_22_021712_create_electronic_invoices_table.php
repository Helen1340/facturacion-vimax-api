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
        Schema::create('electronic_invoices', function (Blueprint $table) {
            $table->id();
            //$table->unsignedBigInteger('user_id');
            //$table->foreignId('user_id')->constrained('users'); // Relación con la tabla 'users'
            $table->string('numero_factura', 20)->unique();
            $table->timestamp('fecha_emision');
            $table->decimal('sub_total', 15, 2);
            $table->decimal('total_impuesto', 15, 2);
            $table->decimal('total_factura', 15, 2);
            $table->enum('estado_interno', ['borrador', 'Emitida']);
            $table->decimal('descuento_total', 15, 2)->nullable();
            $table->string('observacion', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electronic_invoices');
    }
};
