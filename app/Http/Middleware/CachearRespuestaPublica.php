<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cabecera de caché para las rutas públicas (docs/02, sección 8.2: cachear
 * 5 minutos, con purga al publicar un video). Complementa —no reemplaza— la
 * regla de caché que se configura en el dashboard de Cloudflare.
 */
class CachearRespuestaPublica
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethod('GET') && $response->getStatusCode() === 200) {
            $response->headers->set('Cache-Control', 'public, max-age=300');
        }

        return $response;
    }
}
