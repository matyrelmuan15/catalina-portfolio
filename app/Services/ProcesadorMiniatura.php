<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

/**
 * Recorta la miniatura de un video a 9:16 y la convierte a WebP antes de
 * subirla a R2 (docs/04-plan-de-fases.md, fase 3: "una imagen de 5 MB se
 * procesa y queda por debajo de 200 KB").
 */
class ProcesadorMiniatura
{
    private const ANCHO = 720;

    private const ALTO = 1280;

    public function procesarYGuardar(UploadedFile $archivo): string
    {
        $manager = new ImageManager(new Driver);

        $imagen = $manager->read($archivo->getRealPath())
            ->cover(self::ANCHO, self::ALTO);

        $codificada = $imagen->toWebp(quality: 82);

        $ruta = 'miniaturas/'.Str::uuid().'.webp';

        Storage::disk('r2')->put($ruta, (string) $codificada);

        return $ruta;
    }

    public function eliminar(?string $ruta): void
    {
        if ($ruta) {
            Storage::disk('r2')->delete($ruta);
        }
    }
}
