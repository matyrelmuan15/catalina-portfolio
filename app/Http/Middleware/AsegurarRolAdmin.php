<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Protege /panel/*: sin sesión va al ingreso, con sesión pero sin rol admin
 * recibe 403 (criterio de aceptación de la Fase 1, docs/04).
 */
class AsegurarRolAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->guest(route('ingresar'));
        }

        abort_unless($request->user()->esAdmin(), 403);

        return $next($request);
    }
}
