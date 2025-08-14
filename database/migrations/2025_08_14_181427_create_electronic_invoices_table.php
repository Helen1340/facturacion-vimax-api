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

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('customer_id');

            // Foreign keys (actívalas cuando existan las tablas relacionadas)
            // $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            // $table->foreign('customer_id')->references('id')->on('customers')->restrictOnDelete();

            $table->string('numero_factura', 50)->unique();
            $table->date('fecha_emision');
            $table->time('hora_emision');
            $table->char('moneda', 3)->default('COP');
            $table->string('medio_pago', 50);
            $table->Decimal('subtotal', 15, 2);
            $table->Decimal('total_impuesto', 15, 2);
            $table->Decimal('total', 15, 2);
            $table->char('cufe', 96)->unique();
            $table->text('codigo_qr')->nullable();
            $table->longText('xml_firmado');
            $table->enum('estado_dian', ['pendiente', 'enviada', 'aceptada'])->default('pendiente');
            $table->longText('cdr')->nullable();
            $table->enum('modo_emision', ['normal', 'en_contingencia'])->default('normal');
            $table->enum('estado_interno', ['borrador', 'firmada', 'anulada'])->default('borrador');
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
