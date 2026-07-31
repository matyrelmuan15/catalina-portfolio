<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Nivel 2 del aislamiento: nada del panel se sirve sin rol admin, sin
 * importar por qué camino haya llegado la petición.
 *
 * Sin sesión: redirige a ingresar. Con sesión y rol equivocado: 403.
 */
class AsegurarRolAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if ($usuario === null) {
            return redirect()->guest(route('ingresar'));
        }

        abort_if(! $usuario->esAdmin() || ! $usuario->activo, 403);

        return $next($request);
    }
}
