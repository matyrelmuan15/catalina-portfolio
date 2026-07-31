<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Purga la caché de Cloudflare de las rutas públicas cuando se publica o
 * despublica un video (docs/01-especificacion-funcional.md RF-14 y
 * docs/02-arquitectura-y-datos.md §8.2).
 *
 * Sin credenciales de Cloudflare (pendiente de la titular, ver AVANCE.md)
 * la purga queda documentada en el log en vez de fallar: el sitio sigue
 * funcionando, solo que localmente no hay caché de borde que limpiar.
 */
class PurgadorCacheCloudflare
{
    public function purgarPortada(): void
    {
        $zona = config('services.cloudflare.zone_id');
        $token = config('services.cloudflare.api_token');

        if (blank($zona) || blank($token)) {
            Log::info('Purga de caché de Cloudflare omitida: faltan credenciales (ver AVANCE.md).');

            return;
        }

        Http::withToken($token)
            ->post("https://api.cloudflare.com/client/v4/zones/{$zona}/purge_cache", [
                'files' => [url('/')],
            ])
            ->throw();
    }
}
