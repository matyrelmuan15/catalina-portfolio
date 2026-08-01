<?php

use App\Enums\ProveedorVideoTipo;
use App\Services\ProveedorVideo;

beforeEach(function () {
    $this->servicio = new ProveedorVideo;
});

it('detecta YouTube en sus distintos formatos de enlace', function (string $enlace) {
    expect($this->servicio->detectar($enlace))->toBe(ProveedorVideoTipo::Youtube);
})->with([
    'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    'https://youtu.be/dQw4w9WgXcQ',
    'https://www.youtube.com/embed/dQw4w9WgXcQ',
    'https://www.youtube.com/shorts/dQw4w9WgXcQ',
]);

it('detecta Vimeo', function () {
    expect($this->servicio->detectar('https://vimeo.com/900000001'))->toBe(ProveedorVideoTipo::Vimeo);
});

it('trata cualquier otro enlace como archivo', function () {
    expect($this->servicio->detectar('https://cdn.miempresa.com/videos/pieza.mp4'))->toBe(ProveedorVideoTipo::Archivo);
});

it('arma el embed de YouTube sin cookies', function () {
    $url = $this->servicio->urlEmbebido('https://www.youtube.com/watch?v=dQw4w9WgXcQ');

    expect($url)->toBe('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ');
});

it('arma el embed de Vimeo', function () {
    $url = $this->servicio->urlEmbebido('https://vimeo.com/900000001');

    expect($url)->toBe('https://player.vimeo.com/video/900000001');
});

it('para un archivo devuelve el mismo enlace como embed', function () {
    $enlace = 'https://cdn.miempresa.com/videos/pieza.mp4';

    expect($this->servicio->urlEmbebido($enlace))->toBe($enlace);
});

it('resuelve la miniatura automática de YouTube', function () {
    $url = $this->servicio->urlMiniatura('https://youtu.be/dQw4w9WgXcQ');

    expect($url)->toBe('https://i.ytimg.com/vi/dQw4w9WgXcQ/maxresdefault.jpg');
});

it('no resuelve miniatura automática para Vimeo ni archivo (pendiente #6 de AVANCE.md)', function () {
    expect($this->servicio->urlMiniatura('https://vimeo.com/900000001'))->toBeNull();
    expect($this->servicio->urlMiniatura('https://cdn.miempresa.com/videos/pieza.mp4'))->toBeNull();
});

it('devuelve null como embed de YouTube o Vimeo cuando no logra extraer el id', function () {
    expect($this->servicio->urlEmbebido('https://www.youtube.com/'))->toBeNull();
    expect($this->servicio->urlEmbebido('https://vimeo.com/'))->toBeNull();
});
