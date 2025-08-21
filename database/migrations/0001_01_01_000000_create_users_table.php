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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Clave foránea hacia companies
            $table->unsignedBigInteger('company_id')->nullable();
            //$table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            $table->string('nombre', 100);
            $table->enum('tipo_documento', ['NIT', 'CC', 'CE']);
            $table->string('numero_documento', 15);
            $table->string('direccion', 150);
            $table->string('pais', 50);
            $table->string('descripcion', 250);
            $table->string('contrasena', 225);
            $table->string('correo_electronico', 100)->unique();
            $table->string('telefono', 20);
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
            $table->timestamp('ultimo_acceso');

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
