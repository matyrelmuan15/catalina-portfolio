<?php

use App\Livewire\Panel\Videos\Formulario;
use App\Models\User;
use App\Models\Video;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

function crearAdministradora(): User
{
    return User::factory()->admin()->create(['two_factor_secret' => 'secreto-de-prueba']);
}

test('cargar un video nuevo lo crea con los datos del formulario', function () {
    Livewire::actingAs(crearAdministradora())
        ->test(Formulario::class)
        ->set('titulo', 'Rutina de mañana')
        ->set('cliente_texto', 'Bloom Skincare')
        ->set('categoria', 'UGC')
        ->set('fecha', '2026-06-18')
        ->set('enlace', 'https://vimeo.com/123456789')
        ->set('descripcion', 'Tres piezas verticales.')
        ->call('guardar')
        ->assertDispatched('video-guardado');

    $video = Video::firstWhere('titulo', 'Rutina de mañana');

    expect($video)->not->toBeNull();
    expect($video->proveedor)->toBe('vimeo');
    expect($video->publicado)->toBeTrue();
});

test('el titulo, el cliente, la categoria, la fecha y el enlace son obligatorios', function () {
    Livewire::actingAs(crearAdministradora())
        ->test(Formulario::class)
        ->call('guardar')
        ->assertHasErrors(['titulo', 'cliente_texto', 'categoria', 'fecha', 'enlace']);
});

test('editar un video existente actualiza sus datos', function () {
    $video = Video::factory()->create(['titulo' => 'Título viejo']);

    Livewire::actingAs(crearAdministradora())
        ->test(Formulario::class, ['videoId' => $video->id])
        ->set('titulo', 'Título nuevo')
        ->call('guardar')
        ->assertDispatched('video-guardado');

    expect($video->fresh()->titulo)->toBe('Título nuevo');
});

test('subir una miniatura la recorta a 9:16, la convierte a webp y la guarda en el disco r2', function () {
    Storage::fake('r2');
    $imagen = UploadedFile::fake()->image('foto.jpg', 2000, 1000);

    Livewire::actingAs(crearAdministradora())
        ->test(Formulario::class)
        ->set('titulo', 'Con miniatura')
        ->set('cliente_texto', 'Marca de prueba')
        ->set('categoria', 'UGC')
        ->set('fecha', '2026-06-18')
        ->set('enlace', 'https://vimeo.com/123456789')
        ->set('miniatura', $imagen)
        ->call('guardar');

    $video = Video::firstWhere('titulo', 'Con miniatura');

    expect($video->miniatura_path)->not->toBeNull();
    expect($video->miniatura_path)->toEndWith('.webp');
    Storage::disk('r2')->assertExists($video->miniatura_path);
});
