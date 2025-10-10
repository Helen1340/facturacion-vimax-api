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
            
            //  Relación con usuario (de allí se deriva la compañía)
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Información general de la factura
            $table->string('invoice_number', 20)->unique(); //numero_factura
            $table->timestamp('issue_date'); // fecha_emision
            $table->enum('internal_status', ['draft', 'issued', 'cancelled'])->default('draft'); // estado interno
            $table->string('observation', 255)->nullable(); // observacion

            //UBL / Metadata DIAN
            $table->string('ubl_version')->default('2.1');
            $table->string('customization_id')->nullable(); // Ej: DIAN 2.1:Factura Electrónica de Venta
            $table->string('profile_id')->nullable(); // Ej: DIAN 2.1
            $table->string('uuid', 100)->nullable()->unique(); // CUFE - UUID factura (cbc:UUID)
            $table->string('document_currency_code', 3)->default('COP');
            $table->string('invoice_type_code', 10)->default('01'); // 01 = Venta

            //Totales principales (alineado con Anexo Técnico)
            $table->decimal('line_extension_amount', 18, 2)->default(0); // subtotal sin impuestos
            $table->decimal('tax_exclusive_amount', 18, 2)->default(0);  // base imponible
            $table->decimal('tax_inclusive_amount', 18, 2)->default(0);  // total con impuestos
            $table->decimal('payable_amount', 18, 2)->default(0);        // total a pagar

            //Información de pago (solo contado)
            
            $table->string('payment_means_code', 6)->default('10'); // 10 = Efectivo
            $table->string('payment_means_name')->default('Contado');

            // Estado ante la DIAN
            $table->enum('dian_status', ['pending','sent','accepted','rejected','error','cancelled'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('received_at')->nullable();

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
