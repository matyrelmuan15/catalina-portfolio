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
        Schema::create('agenda_eventos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            // Guardada sin hora a propósito: evita que un evento del día 1
            // aparezca el 31 del mes anterior por una conversión de huso horario.
            $table->date('fecha');
            $table->string('tipo', 20);
            $table->string('titulo', 160);
            $table->text('nota')->nullable();
            $table->timestamps();

            $table->index(['cliente_id', 'fecha']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agenda_eventos');
    }
};
