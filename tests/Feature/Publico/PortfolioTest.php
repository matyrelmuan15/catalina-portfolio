<?php

use App\Enums\CategoriaVideo;
use App\Models\Video;

it('responde 200 y muestra solo videos publicados', function () {
    $publicado = Video::factory()->create(['titulo' => 'Pieza publicada']);
    $oculto = Video::factory()->oculto()->create(['titulo' => 'Pieza oculta']);

    $respuesta = $this->get('/');

    $respuesta->assertOk();
    $respuesta->assertSee('Pieza publicada');
    $respuesta->assertDontSee('Pieza oculta');
});

it('ordena primero los destacados y luego por fecha descendente', function () {
    $viejo = Video::factory()->create(['titulo' => 'Video viejo', 'fecha' => '2026-01-01']);
    $nuevo = Video::factory()->create(['titulo' => 'Video nuevo', 'fecha' => '2026-06-01']);
    $destacadoViejo = Video::factory()->destacado()->create(['titulo' => 'Destacado viejo', 'fecha' => '2025-01-01']);

    $html = $this->get('/')->getContent();

    $posDestacado = strpos($html, 'Destacado viejo');
    $posNuevo = strpos($html, 'Video nuevo');
    $posViejo = strpos($html, 'Video viejo');

    expect($posDestacado)->toBeLessThan($posNuevo);
    expect($posNuevo)->toBeLessThan($posViejo);
});

it('muestra un botón de filtro por cada categoría más "Todos"', function () {
    Video::factory()->create();

    $respuesta = $this->get('/');

    $respuesta->assertSee('>Todos<', false);
    foreach (CategoriaVideo::cases() as $categoria) {
        $respuesta->assertSee($categoria->value);
    }
});

it('muestra el botón de iniciar sesión en la cabecera', function () {
    $this->get('/')->assertSee('Iniciar sesión');
});

it('trae las etiquetas Open Graph para compartir el enlace', function () {
    $respuesta = $this->get('/');

    $respuesta->assertSee('property="og:title"', false);
    $respuesta->assertSee('property="og:description"', false);
    $respuesta->assertSee('property="og:url"', false);
});

it('incluye datos estructurados de tipo Person', function () {
    $respuesta = $this->get('/');

    $respuesta->assertSee('"@type":"Person"', false);
    $respuesta->assertSee('Catalina Avenda', false);
});

it('el estado vacío se ve cuando no hay videos publicados', function () {
    $this->get('/')->assertSee('Todavía no hay trabajos publicados.');
});

it('sirve un sitemap.xml válido', function () {
    Video::factory()->create();

    $respuesta = $this->get('/sitemap.xml');

    $respuesta->assertOk();
    $respuesta->assertHeader('Content-Type', 'application/xml');
    $respuesta->assertSee('<urlset', false);
    $respuesta->assertSee(route('portfolio.inicio'), false);
});

it('la página pública se puede cachear cinco minutos', function () {
    $this->get('/')->assertHeader('Cache-Control', 'max-age=300, public');
});

it('robots.txt referencia el sitemap y bloquea las áreas privadas', function () {
    // public/robots.txt lo sirve el servidor web directamente, sin pasar por
    // el enrutador de Laravel: no es alcanzable con una petición de prueba,
    // así que acá se verifica el contenido del archivo.
    $contenido = file_get_contents(public_path('robots.txt'));

    expect($contenido)->toContain('Sitemap: /sitemap.xml');
    expect($contenido)->toContain('Disallow: /panel/');
    expect($contenido)->toContain('Disallow: /portal/');
});
