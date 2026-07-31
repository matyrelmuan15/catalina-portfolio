<?php

use App\Livewire\Panel\Videos\Listado;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Video;
use Livewire\Livewire;

test('un cliente no puede ver el listado de videos del panel', function () {
    $cliente = Cliente::factory()->create();
    $usuario = User::factory()->cliente()->create(['cliente_id' => $cliente->id]);

    $this->actingAs($usuario)->get('/panel/videos')->assertForbidden();
});

test('los cuatro contadores reflejan el estado real', function () {
    Video::factory()->count(2)->create(['publicado' => true, 'destacado' => false]);
    Video::factory()->create(['publicado' => true, 'destacado' => true]);
    Video::factory()->create(['publicado' => false, 'destacado' => false]);

    $admin = User::factory()->admin()->create(['two_factor_secret' => 'secreto-de-prueba']);

    Livewire::actingAs($admin)
        ->test(Listado::class)
        ->assertViewHas('contadores', [
            'cargados' => 4,
            'publicados' => 3,
            'ocultos' => 1,
            'destacados' => 1,
        ]);
});

test('el buscador filtra por titulo o cliente', function () {
    Video::factory()->create(['titulo' => 'Rutina de mañana', 'cliente_texto' => 'Bloom Skincare']);
    Video::factory()->create(['titulo' => 'Otra pieza', 'cliente_texto' => 'Casa Nima']);

    $admin = User::factory()->admin()->create(['two_factor_secret' => 'secreto-de-prueba']);

    Livewire::actingAs($admin)
        ->test(Listado::class)
        ->set('busqueda', 'bloom')
        ->assertViewHas('videos', fn ($videos) => $videos->count() === 1);
});

test('el filtro por categoria funciona', function () {
    Video::factory()->create(['categoria' => 'UGC']);
    Video::factory()->create(['categoria' => 'Fotografía']);

    $admin = User::factory()->admin()->create(['two_factor_secret' => 'secreto-de-prueba']);

    Livewire::actingAs($admin)
        ->test(Listado::class)
        ->set('categoriaFiltro', 'UGC')
        ->assertViewHas('videos', fn ($videos) => $videos->count() === 1);
});

test('alternar publicado cambia el estado sin abrir el formulario', function () {
    $video = Video::factory()->create(['publicado' => true]);
    $admin = User::factory()->admin()->create(['two_factor_secret' => 'secreto-de-prueba']);

    Livewire::actingAs($admin)
        ->test(Listado::class)
        ->call('alternarPublicado', $video->id);

    expect($video->fresh()->publicado)->toBeFalse();
});

test('alternar destacado cambia el estado', function () {
    $video = Video::factory()->create(['destacado' => false]);
    $admin = User::factory()->admin()->create(['two_factor_secret' => 'secreto-de-prueba']);

    Livewire::actingAs($admin)
        ->test(Listado::class)
        ->call('alternarDestacado', $video->id);

    expect($video->fresh()->destacado)->toBeTrue();
});

test('eliminar un video lo borra en forma definitiva', function () {
    $video = Video::factory()->create();
    $admin = User::factory()->admin()->create(['two_factor_secret' => 'secreto-de-prueba']);

    Livewire::actingAs($admin)
        ->test(Listado::class)
        ->call('eliminar', $video->id);

    expect(Video::find($video->id))->toBeNull();
});
