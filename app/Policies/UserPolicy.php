<?php

namespace App\Policies;

use App\Models\User;

/**
 * Nivel 2 del aislamiento entre clientes (docs/02, sección 6.1): toda acción
 * sobre la cuenta de un usuario verifica pertenencia antes de ejecutarse. La
 * administradora gestiona cualquier cuenta; un cliente solo la propia.
 */
class UserPolicy
{
    public function view(User $usuario, User $objetivo): bool
    {
        return $usuario->esAdmin() || $usuario->id === $objetivo->id;
    }

    public function update(User $usuario, User $objetivo): bool
    {
        return $usuario->esAdmin() || $usuario->id === $objetivo->id;
    }
}
