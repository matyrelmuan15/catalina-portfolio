<?php

namespace App\Services;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

/**
 * Recorta la miniatura a 9:16 y la convierte a WebP, bajando calidad y
 * resolución hasta que quede por debajo de 200 KB (criterio de aceptación
 * de la Fase 3: una imagen de 5 MB tiene que quedar por debajo de eso).
 */
class ProcesadorMiniatura
{
    private const ANCHO = 720;

    private const ALTO = 1280;

    private const LIMITE_BYTES = 200 * 1024;

    private ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver);
    }

    public function procesar(string $rutaOrigen): string
    {
        $ancho = self::ANCHO;
        $alto = self::ALTO;
        $calidad = 82;
        $binario = '';

        for ($intento = 0; $intento < 12; $intento++) {
            $binario = (string) $this->manager->read($rutaOrigen)
                ->cover($ancho, $alto)
                ->toWebp(quality: max($calidad, 20));

            if (strlen($binario) <= self::LIMITE_BYTES) {
                return $binario;
            }

            if ($calidad > 30) {
                $calidad -= 12;
            } else {
                $ancho = (int) round($ancho * 0.85);
                $alto = (int) round($alto * 0.85);
                $calidad = 60;
            }
        }

        // Mejor esfuerzo si se agotaron los intentos: igual es la versión
        // más liviana que se pudo generar.
        return $binario;
    }
}
