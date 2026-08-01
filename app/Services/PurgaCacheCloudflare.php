<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Purga la caché de Cloudflare para la portada del portfolio cuando un video
 * se publica o se despublica (docs/02, sección 8.2: cachear una respuesta
 * del portal a un cliente sería el error más grave; acá el riesgo simétrico
 * es servir una portada vieja después de publicar o despublicar).
 */
class PurgaCacheCloudflare
{
    public function purgarPortada(): void
    {
        $zona = config('services.cloudflare.zone_id');
        $token = config('services.cloudflare.api_token');

        if (! $zona || ! $token) {
            Log::info('Purga de caché de Cloudflare omitida: faltan credenciales en este entorno.');

            return;
        }

        Http::withToken($token)
            ->post("https://api.cloudflare.com/client/v4/zones/{$zona}/purge_cache", [
                'files' => [route('portfolio.inicio')],
            ]);
    }
}
