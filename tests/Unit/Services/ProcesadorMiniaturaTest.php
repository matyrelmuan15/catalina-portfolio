<?php

use App\Services\ProcesadorMiniatura;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

/**
 * Genera una imagen de referencia "realista" (con ruido y degradés, no un
 * color plano que comprimiría trivialmente) de unos 5 MB para probar el
 * criterio de aceptación de la Fase 3: tiene que quedar por debajo de 200 KB.
 */
function imagenDePruebaDe5MB(): string
{
    $ruta = tempnam(sys_get_temp_dir(), 'miniatura_prueba').'.png';

    $ancho = 2400;
    $alto = 3200;
    $lienzo = imagecreatetruecolor($ancho, $alto);

    for ($y = 0; $y < $alto; $y += 4) {
        for ($x = 0; $x < $ancho; $x += 4) {
            $r = (int) (($x / $ancho) * 255) ^ random_int(0, 60);
            $g = (int) (($y / $alto) * 255) ^ random_int(0, 60);
            $b = random_int(0, 255);
            $color = imagecolorallocate($lienzo, min($r, 255), min($g, 255), $b);
            imagefilledrectangle($lienzo, $x, $y, $x + 3, $y + 3, $color);
        }
    }

    imagepng($lienzo, $ruta, 0);
    imagedestroy($lienzo);

    return $ruta;
}

it('deja una imagen de unos 5 MB por debajo de 200 KB, recortada a 9:16', function () {
    $ruta = imagenDePruebaDe5MB();

    try {
        $binario = (new ProcesadorMiniatura)->procesar($ruta);

        expect(strlen($binario))->toBeLessThan(200 * 1024);

        $manager = new ImageManager(new Driver);
        $resultado = $manager->read($binario);

        expect(round($resultado->width() / $resultado->height(), 2))->toBe(round(9 / 16, 2));
    } finally {
        @unlink($ruta);
    }
});
