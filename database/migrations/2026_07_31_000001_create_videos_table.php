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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 120);
            // Texto libre: aparecen marcas que no son clientes del portal (docs/01 §4.1).
            $table->string('cliente_texto', 120);
            $table->string('categoria', 30);
            $table->date('fecha');
            $table->string('proveedor', 20)->default('youtube');
            $table->string('enlace');
            $table->string('miniatura_path')->nullable();
            $table->string('descripcion', 500)->nullable();
            $table->boolean('publicado')->default(true);
            $table->boolean('destacado')->default(false);
            $table->integer('orden')->default(0);
            $table->timestamps();

            $table->index(['publicado', 'destacado', 'fecha']);
            $table->index('categoria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
