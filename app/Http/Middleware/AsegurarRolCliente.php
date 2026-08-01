<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Protege /portal/*: un visitante sin sesión va al ingreso (RF de la Fase 1);
 * una sesión con otro rol recibe 403.
 */
class AsegurarRolCliente
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->guest(route('ingresar'));
        }

        abort_unless($request->user()->esCliente(), 403);

        return $next($request);
    }
}
