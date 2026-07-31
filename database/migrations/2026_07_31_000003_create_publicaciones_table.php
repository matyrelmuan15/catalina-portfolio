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
        Schema::create('publicaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();

            // Grupo C — propio, se carga a mano (docs/01-especificacion-funcional.md §7.1).
            $table->string('titulo', 160);
            $table->string('estado', 20)->default('Planificada');
            $table->string('pilar', 40)->nullable();
            $table->text('archivo_final_url')->nullable();
            $table->text('creativo_figma_url')->nullable();

            // Grupo A — de Meta. Se completan al importar (fase 7); acá se
            // precargan a mano para lo que todavía no se importó.
            $table->date('fecha');
            $table->string('plataforma', 20)->default('instagram');
            $table->string('formato', 20)->default('reel');
            $table->text('copy_texto')->nullable();
            $table->text('hashtags')->nullable();
            $table->string('id_media', 40)->nullable();
            $table->string('permalink', 300)->nullable();

            $table->timestamps();

            $table->index(['cliente_id', 'fecha']);
            $table->index('permalink');
            $table->unique('id_media');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publicaciones');
    }
};
