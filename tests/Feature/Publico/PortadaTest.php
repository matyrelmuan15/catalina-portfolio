<?php

use App\Livewire\Publico\Portada;
use App\Models\Video;
use Livewire\Livewire;

test('la portada responde', function () {
    $this->get('/')->assertOk();
});

test('la grilla solo muestra videos publicados', function () {
    Video::factory()->count(3)->create(['publicado' => true]);
    Video::factory()->count(2)->create(['publicado' => false]);

    Livewire::test(Portada::class)
        ->assertViewHas('videos', fn ($videos) => $videos->count() === 3);
});

test('los destacados van primero y despues por fecha descendente', function () {
    $viejo = Video::factory()->create(['fecha' => '2026-01-01', 'destacado' => false]);
    $nuevo = Video::factory()->create(['fecha' => '2026-06-01', 'destacado' => false]);
    $destacadoViejo = Video::factory()->create(['fecha' => '2025-01-01', 'destacado' => true]);

    $orden = Livewire::test(Portada::class)->viewData('videos')->pluck('id')->all();

    expect($orden)->toBe([$destacadoViejo->id, $nuevo->id, $viejo->id]);
});

test('la portada trae las etiquetas open graph', function () {
    $response = $this->get('/');

    $response->assertSee('og:title', false);
    $response->assertSee('og:image', false);
    $response->assertSee('application/ld+json', false);
});

test('el sitemap responde en xml', function () {
    $response = $this->get('/sitemap.xml');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/xml');
});

test('robots.txt bloquea panel y portal', function () {
    $contenido = file_get_contents(public_path('robots.txt'));

    expect($contenido)->toContain('Disallow: /panel');
    expect($contenido)->toContain('Disallow: /portal');
});
