<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * Nivel 1 del aislamiento entre clientes (docs/02-arquitectura-y-datos.md §6.1).
 *
 * Se aplica a todo modelo con columna "cliente_id". Cuando el usuario autenticado
 * tiene rol cliente, toda consulta queda filtrada por su cliente_id de forma
 * automática, sin depender de que quien programa lo recuerde en cada lugar.
 */
class PerteneceAlCliente implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $usuario = Auth::user();

        if ($usuario === null || ! $usuario->esCliente()) {
            return;
        }

        $columna = $model->getTable().'.cliente_id';

        $builder->getQuery()->where($columna, '=', $usuario->cliente_id);
    }
}
