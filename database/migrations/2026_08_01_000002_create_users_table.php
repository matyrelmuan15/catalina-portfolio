<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('email', 180)->unique();
            $table->string('password', 255);
            $table->string('rol', 20)->default('cliente');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->cascadeOnDelete();
            $table->boolean('activo')->default(true);
            $table->text('two_factor_secret')->nullable();
            $table->timestampTz('ultimo_acceso_at')->nullable();
            $table->timestamps();

            $table->index('rol');
        });

        // Un cliente siempre tiene marca asociada; una administradora, nunca.
        // Defensa a nivel de base de datos de la regla de la sección 3.1 de docs/02,
        // además de la validación de la capa de aplicación.
        DB::statement(<<<'SQL'
            ALTER TABLE users ADD CONSTRAINT users_cliente_id_segun_rol_check
            CHECK (
                (rol = 'cliente' AND cliente_id IS NOT NULL)
                OR (rol = 'admin' AND cliente_id IS NULL)
            )
        SQL);

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

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
