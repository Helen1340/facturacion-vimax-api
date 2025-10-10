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

    // Relaciones
            $table->unsignedBigInteger('company_id')->nullable(); // FK a la compañía
            $table->unsignedBigInteger('role_id')->nullable();    // FK al rol del usuario

            // Datos personales
            $table->string('first_name', 100); // Nombre del usuario
            $table->enum('document_type', ['NIT', 'CC', 'CE'])->nullable(); // Tipo de documento
            $table->string('document_number', 50)->unique(); // Número de documento único
            $table->string('address', 150)->nullable(); // Dirección
            $table->string('country', 100)->nullable(); // País de residencia
            $table->string('description', 250)->nullable(); // Descripción opcional
            $table->string('password', 255); // Contraseña encriptada
            $table->string('email', 150)->unique(); // Correo electrónico único
            $table->string('phone', 20)->nullable(); // Teléfono de contacto
            $table->enum('status', ['Active', 'Inactive'])->default('Active'); // Estado del usuario
            $table->timestamp('last_access')->nullable(); // Último acceso al sistema

            $table->rememberToken(); // Token para "recordar sesión"
            $table->timestamps();    // created_at y updated_at
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
