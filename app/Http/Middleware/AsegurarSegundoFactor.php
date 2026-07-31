<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * RF-93: segundo factor obligatorio para la administradora. Si todavía no lo
 * activó, la única pantalla del panel a la que puede entrar es la de
 * configurarlo.
 */
class AsegurarSegundoFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if ($usuario !== null
            && $usuario->esAdmin()
            && ! $usuario->tieneSegundoFactorActivo()
            && ! $request->routeIs('panel.cuenta.dos-factores')
        ) {
            return redirect()->route('panel.cuenta.dos-factores');
        }

        return $next($request);
    }
}
