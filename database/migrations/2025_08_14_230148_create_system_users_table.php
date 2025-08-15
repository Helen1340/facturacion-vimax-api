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
        Schema::create('system_users', function (Blueprint $table) {
            $table->id();

            $table->string('IdUsuario')->unique();
            $table->string('NombreCompleto');
            $table->enum('Rol', ['Admin', 'Facturador']);
            $table->string('Contraseña');
            $table->string('CorreoElectronico');
            $table->string('Teléfono');
            $table->boolean('Estado');
            $table->timestamp('UltimoAcceso')->nullable();
            $table->timestamp('FechaCreación')->useCurrent();
            $table->timestamp('FechaActualización')->nullable();
            $table->string('NumeroIdentificacion')->unique();

            // FK: Rol
            // $table->unsignedBigInteger('rol_id');
            // $table->foreign('rol_id')->references('id')->on('roles')->onDelete('cascade');

            // FK: NIT de Empresa
            // $table->string('empresa_nit');
            // $table->foreign('empresa_nit')->references('NIT')->on('empresas')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_users');
    }
};
