<?php

namespace App\Policies;

use App\Models\Cliente;
use App\Models\User;

/**
 * Nivel 2 del aislamiento (docs/02-arquitectura-y-datos.md §6.1): toda acción
 * sobre un cliente verifica pertenencia antes de ejecutarse, aunque el
 * identificador haya llegado directo por la URL.
 */
class ClientePolicy
{
    public function verCualquiera(User $usuario): bool
    {
        return $usuario->esAdmin();
    }

    public function ver(User $usuario, Cliente $cliente): bool
    {
        if ($usuario->esAdmin()) {
            return true;
        }

        return $usuario->esCliente() && $usuario->cliente_id === $cliente->id;
    }

    public function crear(User $usuario): bool
    {
        return $usuario->esAdmin();
    }

    public function actualizar(User $usuario, Cliente $cliente): bool
    {
        return $usuario->esAdmin();
    }

    public function archivar(User $usuario, Cliente $cliente): bool
    {
        return $usuario->esAdmin();
    }

    public function restablecerClave(User $usuario, Cliente $cliente): bool
    {
        return $usuario->esAdmin();
    }
}
