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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('marca', 120);
            $table->string('contacto', 120);
            $table->string('correo_contacto', 180);
            $table->string('telefono', 40)->nullable();
            $table->string('color', 20)->default('fucsia');
            $table->boolean('archivado')->default(false);
            $table->timestamps();

            $table->index('archivado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
