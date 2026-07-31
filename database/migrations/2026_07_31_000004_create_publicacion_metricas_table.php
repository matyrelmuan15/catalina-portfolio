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
        // Una fila por medición: permite ver la evolución de una pieza en el
        // tiempo, no solo su último estado (docs/02-arquitectura-y-datos.md §3.1).
        Schema::create('publicacion_metricas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('publicacion_id')->constrained('publicaciones')->cascadeOnDelete();
            $table->date('medido_el');

            // Nulables a propósito: la ausencia de dato es informacion
            // distinta de un cero (docs/01-especificacion-funcional.md §7.4).
            $table->unsignedBigInteger('alcance')->nullable();
            $table->unsignedBigInteger('vistas')->nullable();
            $table->unsignedBigInteger('interacciones')->nullable();
            $table->unsignedBigInteger('me_gusta')->nullable();
            $table->unsignedBigInteger('comentarios')->nullable();
            $table->unsignedBigInteger('compartidos')->nullable();
            $table->unsignedBigInteger('guardados')->nullable();
            $table->unsignedBigInteger('clics_enlace')->nullable();
            $table->unsignedBigInteger('seguidores_al_publicar')->nullable();
            $table->decimal('tasa_interaccion', 5, 2)->nullable();

            $table->timestamps();

            $table->unique(['publicacion_id', 'medido_el']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publicacion_metricas');
    }
};
