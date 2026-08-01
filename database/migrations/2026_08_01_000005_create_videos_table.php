<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 120)->collation('es-AR-x-icu');
            // Texto libre, no es relación: aparecen marcas que no son clientes del portal.
            $table->string('cliente_texto', 120);
            $table->string('categoria', 20);
            $table->date('fecha');
            $table->string('proveedor', 20)->default('youtube');
            $table->string('enlace', 500);
            $table->string('miniatura_path', 255)->nullable();
            $table->string('descripcion', 500)->nullable();
            $table->boolean('publicado')->default(true);
            $table->boolean('destacado')->default(false);
            $table->integer('orden')->default(0);
            $table->timestamps();

            $table->index(['publicado', 'destacado', 'fecha']);
            $table->index('categoria');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
