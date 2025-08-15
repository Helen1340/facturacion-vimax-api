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
            $table->string('nombre_completo', 100);
            $table->enum('rol', ['Admin', 'Facturador']);
            $table->string('contrasena', 225);
            $table->string('correo_electronico', 100);
            $table->string('telefono', 20);
            $table->boolean('estado');
            $table->timestamp('ultimo_acceso')->nullable();
            $table->string('numero_identificacion', 15);

            // FK: Rol
            // $table->unsignedBigInteger('role_id');
            // $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');

            // FK: NIT de Empresa
            // $table->string('Company_id');
            // $table->foreign('Company_id')->references('id')->on('Companys')->onDelete('cascade');

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
