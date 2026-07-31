<?php

namespace App\Concerns;

use App\Scopes\PerteneceAlCliente;
use Illuminate\Support\Facades\Auth;

/**
 * Se agrega a todo modelo con columna "cliente_id". Aplica el global scope de
 * aislamiento (nivel 1) y, de yapa, completa el cliente_id solo al crear un
 * registro estando autenticado como cliente, para que el formulario no tenga
 * que mandarlo nunca.
 */
trait PerteneceACliente
{
    public static function bootPerteneceACliente(): void
    {
        static::addGlobalScope(new PerteneceAlCliente);

        static::creating(function ($modelo) {
            $usuario = Auth::user();

            if ($usuario !== null && $usuario->esCliente() && blank($modelo->cliente_id)) {
                $modelo->cliente_id = $usuario->cliente_id;
            }
        });
    }
}
