<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Nivel 2 del aislamiento para el portal de clientes.
 *
 * Sin sesión: redirige a ingresar. Con sesión y rol equivocado, o cliente
 * archivado: 403. El cliente activo nunca se determina por un parámetro de
 * la petición, siempre por la sesión (nivel 3, ver PerteneceAlCliente).
 */
class AsegurarRolCliente
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if ($usuario === null) {
            return redirect()->guest(route('ingresar'));
        }

        abort_if(! $usuario->esCliente() || ! $usuario->activo, 403);
        abort_if($usuario->cliente === null || $usuario->cliente->archivado, 403);

        return $next($request);
    }
}
