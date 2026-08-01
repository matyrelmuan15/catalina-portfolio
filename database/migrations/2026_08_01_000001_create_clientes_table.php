<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            // Collation es-AR-x-icu (regla 2, docs/02 sección 3.3): sin esto el orden
            // alfabético depende del sistema operativo y rompe con tildes y eñes.
            $table->string('marca', 120)->collation('es-AR-x-icu');
            $table->string('contacto', 120)->collation('es-AR-x-icu');
            $table->string('correo_contacto', 180)->nullable();
            $table->string('telefono', 40)->nullable();
            $table->string('color', 20)->default('fucsia');
            $table->boolean('archivado')->default(false);
            $table->timestamps();

            $table->index('archivado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
