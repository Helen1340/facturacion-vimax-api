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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            $table->string('Nombre_Completo', 100);
            $table->string('Correo_Electronico', 100)->unique();
            $table->string('Telefono', 15);
            $table->string('Razon_Social', 150);
            $table->enum('Tipo_Persona', ['Natural', 'Juridica']);
            $table->string('Tipo_Documento', 20);
            $table->string('Observacion', 255)->nullable();
            $table->boolean('Estado')->default(false);
            $table->string('Direccion', 150);
            $table->string('Pais', 50);
            $table->string('Departamento', 50);
            $table->date('Fecha');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
