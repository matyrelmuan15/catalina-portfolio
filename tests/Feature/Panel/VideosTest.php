<?php

use App\Livewire\Panel\Videos\Listado;
use App\Models\User;
use App\Models\Video;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    Storage::fake('r2_publico');
});

it('muestra los cuatro contadores del listado', function () {
    Video::factory()->count(2)->create();
    Video::factory()->count(3)->oculto()->create();
    Video::factory()->destacado()->create();

    $this->actingAs($this->admin);

    Livewire::test(Listado::class)
        ->assertViewHas('totalCargados', 6)
        ->assertViewHas('totalPublicados', 3)
        ->assertViewHas('totalOcultos', 3)
        ->assertViewHas('totalDestacados', 1);
});

it('busca por título o por cliente sin importar mayúsculas (regla 1 de docs/02)', function () {
    Video::factory()->create(['titulo' => 'Rutina de Mañana', 'cliente_texto' => 'Bloom Skincare']);
    Video::factory()->create(['titulo' => 'Otra pieza', 'cliente_texto' => 'Casa Nima']);

    $this->actingAs($this->admin);

    Livewire::test(Listado::class)
        ->set('busqueda', 'rutina')
        ->assertSee('Rutina de Mañana')
        ->assertDontSee('Otra pieza');

    Livewire::test(Listado::class)
        ->set('busqueda', 'BLOOM')
        ->assertSee('Rutina de Mañana')
        ->assertDontSee('Otra pieza');
});

it('filtra por categoría', function () {
    Video::factory()->create(['titulo' => 'Pieza UGC', 'categoria' => 'UGC']);
    Video::factory()->create(['titulo' => 'Pieza Redes', 'categoria' => 'Redes']);

    $this->actingAs($this->admin);

    Livewire::test(Listado::class)
        ->set('categoriaFiltro', 'Redes')
        ->assertSee('Pieza Redes')
        ->assertDontSee('Pieza UGC');
});

it('crea un video nuevo con los datos del formulario', function () {
    $this->actingAs($this->admin);

    Livewire::test(Listado::class)
        ->call('nuevo')
        ->set('titulo', 'Pieza nueva')
        ->set('clienteTexto', 'Marca de prueba')
        ->set('categoria', 'Marketing')
        ->set('fecha', '2026-07-01')
        ->set('enlace', 'https://vimeo.com/123456789')
        ->set('descripcion', 'Una descripción de prueba')
        ->set('publicado', true)
        ->call('guardar')
        ->assertSet('formularioAbierto', false);

    $this->assertDatabaseHas('videos', [
        'titulo' => 'Pieza nueva',
        'cliente_texto' => 'Marca de prueba',
        'categoria' => 'Marketing',
        'proveedor' => 'vimeo',
    ]);
});

it('no crea el video si falta un campo obligatorio', function () {
    $this->actingAs($this->admin);

    Livewire::test(Listado::class)
        ->call('nuevo')
        ->set('titulo', '')
        ->set('clienteTexto', 'Marca')
        ->set('categoria', 'Marketing')
        ->set('fecha', '2026-07-01')
        ->set('enlace', 'https://vimeo.com/123456789')
        ->call('guardar')
        ->assertHasErrors('titulo');

    expect(Video::count())->toBe(0);
});

it('edita un video existente', function () {
    $video = Video::factory()->create(['titulo' => 'Título viejo']);

    $this->actingAs($this->admin);

    Livewire::test(Listado::class)
        ->call('editar', $video->id)
        ->assertSet('titulo', 'Título viejo')
        ->set('titulo', 'Título nuevo')
        ->call('guardar');

    expect($video->fresh()->titulo)->toBe('Título nuevo');
});

it('sube una miniatura, la procesa a WebP y la guarda en el disco público', function () {
    $this->actingAs($this->admin);

    Livewire::test(Listado::class)
        ->call('nuevo')
        ->set('titulo', 'Con miniatura')
        ->set('clienteTexto', 'Marca')
        ->set('categoria', 'UGC')
        ->set('fecha', '2026-07-01')
        ->set('enlace', 'https://vimeo.com/123456789')
        ->set('miniatura', UploadedFile::fake()->image('foto.jpg', 1200, 1600)->size(500))
        ->call('guardar');

    $video = Video::where('titulo', 'Con miniatura')->firstOrFail();

    expect($video->miniatura_path)->not->toBeNull();
    expect($video->miniatura_path)->toEndWith('.webp');
    Storage::disk('r2_publico')->assertExists($video->miniatura_path);
});

it('alterna publicado y destacado desde el listado', function () {
    $video = Video::factory()->create(['publicado' => true, 'destacado' => false]);

    $this->actingAs($this->admin);

    $prueba = Livewire::test(Listado::class);
    $prueba->call('alternarDestacado', $video->id);
    expect($video->fresh()->destacado)->toBeTrue();

    $prueba->call('alternarPublicado', $video->id);
    expect($video->fresh()->publicado)->toBeFalse();
});

it('purga la caché de Cloudflare al cambiar el estado de publicación', function () {
    config(['services.cloudflare.zone_id' => 'zona-de-prueba', 'services.cloudflare.api_token' => 'token-de-prueba']);
    Http::fake(['api.cloudflare.com/*' => Http::response(['success' => true])]);

    $video = Video::factory()->create(['publicado' => true]);

    $this->actingAs($this->admin);

    Livewire::test(Listado::class)->call('alternarPublicado', $video->id);

    Http::assertSent(fn ($request) => str_contains($request->url(), 'purge_cache'));
});

it('pide confirmación antes de eliminar y borra al confirmar', function () {
    $video = Video::factory()->create();

    $this->actingAs($this->admin);

    Livewire::test(Listado::class)
        ->call('confirmarEliminar', $video->id)
        ->assertSet('confirmandoEliminarId', $video->id)
        ->call('eliminar');

    $this->assertDatabaseMissing('videos', ['id' => $video->id]);
});
