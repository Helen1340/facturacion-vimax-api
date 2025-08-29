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
        Schema::create('invoice_details', function (Blueprint $table) {
            $table->id();
             // FK hacia la factura electrónica
           // $table->foreignId('electronic_invoice_id')
             //     ->constrained('electronic_invoices')
             //     ->onDelete('cascade');
            $table->text('descripcion')->nullable();
            $table->integer('cantidad')->default(1);
            $table->decimal('precio_unitario', 15, 2)->default(0);
            $table->decimal('valor_total', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->nullable();
            $table->decimal('descuento', 15, 2)->nullable();
            $table->text('impuestos_aplicados')->nullable(); // json o texto con impuestos
            $table->decimal('valor_impuesto', 15, 2)->nullable();

            // Campos polimórficos (item = product OR service)
            $table->unsignedBigInteger('item_id');
            $table->string('item_type'); // guarda el FQCN del modelo (App\Models\Product o App\Models\Service)
            $table->index(['item_id', 'item_type']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_details');
    }
};
