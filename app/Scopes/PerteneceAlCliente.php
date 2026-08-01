<?php

namespace App\Scopes;

use App\Enums\RolUsuario;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * Nivel 1 del aislamiento entre clientes (docs/02, sección 6.1): cuando el
 * usuario autenticado tiene rol cliente, toda consulta sobre un modelo con
 * `cliente_id` se filtra automáticamente por su marca, sin depender de que
 * quien programe se acuerde de hacerlo a mano.
 *
 * Se aplica también al modelo User, que tiene `cliente_id`. La guarda de
 * `Auth::hasUser()` es lo que lo hace posible: el guard de sesión resuelve al
 * usuario autenticado con una consulta nueva sobre User
 * (EloquentUserProvider::retrieveById) y, mientras esa consulta corre, el guard
 * todavía no tiene usuario asignado. Sin la guarda, el scope llamaría a
 * Auth::user() dentro de la consulta que está resolviendo Auth::user():
 * recursión infinita en cada request autenticado. Con ella, el scope se salta
 * exactamente en ese instante y vuelve a aplicar en todas las demás consultas.
 */
class PerteneceAlCliente implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Mientras el guard está resolviendo la sesión no hay usuario todavía:
        // salir acá evita la recursión al consultar User (ver nota de arriba).
        if (! Auth::hasUser()) {
            return;
        }

        $usuario = Auth::user();

        if ($usuario && $usuario->rol === RolUsuario::Cliente) {
            $builder->where($model->qualifyColumn('cliente_id'), $usuario->cliente_id);
        }
    }
}
