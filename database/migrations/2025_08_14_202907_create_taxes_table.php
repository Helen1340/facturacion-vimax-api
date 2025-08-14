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
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();

            $table->string('codigo_dian', 20)->unique();

            $table->string('nombre', 100);

            $table->text('descripcion')->nullable();

            $table->enum('tipo_aplicacion', ['trasladado', 'retenido']);

            $table->decimal('porcentaje_base', 5, 2)->comment('Porcentaje, hasta 100.00');

            $table->boolean('estado')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};
