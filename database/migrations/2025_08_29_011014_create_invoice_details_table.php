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

            $table->string('item_type'); // guarda el FQCN del modelo (App\Models\Product o App\Models\Service)
            // Campos polimórficos (item = product OR service)
            $table->unsignedBigInteger('item_id');
            $table->index(['item_id', 'item_type']);

            // Relación con la factura electrónica
            $table->foreignId('electronic_invoice_id')->constrained('electronic_invoices')->onDelete('cascade');

            // --- Campos según UBL / DIAN ---
            // --- Campos específicos del detalle (UBL - sin redundancias) ---
            $table->text('description')->nullable(); // Descripción del ítem (campo <Description>)
            $table->integer('quantity')->default(1); // Cantidad (campo <InvoicedQuantity>)
            $table->decimal('unit_price', 15, 2)->default(0); // Precio unitario (campo <PriceAmount>)
            $table->decimal('line_extension_amount', 15, 2)->default(0); // Subtotal sin impuestos (<LineExtensionAmount>)
            $table->decimal('discount_amount', 15, 2)->nullable(); // Descuento aplicado (<AllowanceCharge>)
            $table->decimal('tax_amount', 15, 2)->nullable(); // Impuesto aplicado (<TaxTotal>)
            $table->decimal('total_line_amount', 15, 2)->nullable(); // Total línea (subtotal + impuestos)

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
