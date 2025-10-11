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
        Schema::create('dian_credentials', function (Blueprint $table) {
            $table->id();
            // Relación con la tabla de empresas
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')
                  ->references('id')
                  ->on('companies')
                  ->onDelete('cascade');

            // Campos de credenciales DIAN
            $table->enum('ambiente', ['pruebas', 'produccion'])->default('pruebas'); // Ambiente DIAN
            $table->string('url_point', 255); // URL del punto de conexión DIAN (endpoint)
            $table->string('usuario', 100)->nullable(); // Usuario de autenticación
            $table->string('password', 150)->nullable(); // Contraseña o token
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo'); // Estado de la credencial

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dian_credentials');
    }
};
