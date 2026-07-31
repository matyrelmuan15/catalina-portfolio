<?php

use App\Models\Cliente;
use App\Models\User;

/**
 * Nivel 2 del aislamiento: la policy verifica pertenencia aunque el
 * identificador llegue directo, sin pasar por el global scope (por ejemplo
 * porque la consulta la arma la administradora).
 */
test('un cliente puede ver su propia ficha', function () {
    $cliente = Cliente::factory()->create();
    $usuario = User::factory()->cliente()->create(['cliente_id' => $cliente->id]);

    expect($usuario->can('ver', $cliente))->toBeTrue();
});

test('un cliente no puede ver la ficha de otra marca', function () {
    $clienteA = Cliente::factory()->create();
    $clienteB = Cliente::factory()->create();
    $usuarioA = User::factory()->cliente()->create(['cliente_id' => $clienteA->id]);

    expect($usuarioA->can('ver', $clienteB))->toBeFalse();
});

test('un cliente no puede actualizar ni archivar ninguna ficha', function () {
    $cliente = Cliente::factory()->create();
    $usuario = User::factory()->cliente()->create(['cliente_id' => $cliente->id]);

    expect($usuario->can('actualizar', $cliente))->toBeFalse();
    expect($usuario->can('archivar', $cliente))->toBeFalse();
    expect($usuario->can('restablecerClave', $cliente))->toBeFalse();
});

test('la administradora puede administrar cualquier ficha de cliente', function () {
    $cliente = Cliente::factory()->create();
    $admin = User::factory()->admin()->create();

    expect($admin->can('ver', $cliente))->toBeTrue();
    expect($admin->can('actualizar', $cliente))->toBeTrue();
    expect($admin->can('archivar', $cliente))->toBeTrue();
    expect($admin->can('restablecerClave', $cliente))->toBeTrue();
});
