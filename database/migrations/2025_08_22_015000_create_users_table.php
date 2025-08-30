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

    $table->unsignedBigInteger('company_id')->nullable();
    $table->unsignedBigInteger('role_id')->nullable();

    $table->string('nombre', 100);

    // 👇 ahora permite NULL
    $table->enum('tipo_documento', ['NIT', 'CC', 'CE'])->nullable();
    $table->string('numero_documento', 50)->unique();    // antes 15
    $table->string('direccion', 150)->nullable();
    $table->string('pais', 100)->nullable();
    $table->string('descripcion', 250)->nullable();
    $table->string('contrasena', 225);
    $table->string('correo_electronico', 150)->unique(); // antes 100
    $table->string('telefono', 20)->nullable();
    $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
    $table->timestamp('ultimo_acceso')->nullable();      // antes NOT NULL

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
