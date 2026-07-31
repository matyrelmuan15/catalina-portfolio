<?php

use App\Models\Cliente;
use App\Models\User;

/**
 * Nivel 1 del aislamiento (docs/02-arquitectura-y-datos.md §6.1): el global
 * scope PerteneceAlCliente filtra toda consulta Eloquent sobre un modelo con
 * cliente_id cuando quien consulta tiene rol cliente. Users es, en la fase 1,
 * el único modelo con cliente_id disponible para probarlo; cada modelo nuevo
 * con cliente_id suma su propia prueba en el mismo commit que lo agrega.
 */
test('un cliente autenticado solo ve usuarios de su propio cliente', function () {
    $clienteA = Cliente::factory()->create();
    $clienteB = Cliente::factory()->create();

    $usuarioA = User::factory()->cliente()->create(['cliente_id' => $clienteA->id]);
    User::factory()->cliente()->create(['cliente_id' => $clienteB->id]);

    $this->actingAs($usuarioA);

    $visibles = User::all();

    expect($visibles)->toHaveCount(1);
    expect($visibles->first()->id)->toBe($usuarioA->id);
});

test('un cliente autenticado no puede recuperar por id un usuario de otro cliente', function () {
    $clienteA = Cliente::factory()->create();
    $clienteB = Cliente::factory()->create();

    $usuarioA = User::factory()->cliente()->create(['cliente_id' => $clienteA->id]);
    $usuarioB = User::factory()->cliente()->create(['cliente_id' => $clienteB->id]);

    $this->actingAs($usuarioA);

    expect(User::find($usuarioB->id))->toBeNull();
});

test('la administradora no queda restringida por el global scope', function () {
    $clienteA = Cliente::factory()->create();
    $clienteB = Cliente::factory()->create();
    User::factory()->cliente()->create(['cliente_id' => $clienteA->id]);
    User::factory()->cliente()->create(['cliente_id' => $clienteB->id]);

    $admin = User::factory()->admin()->create(['two_factor_secret' => 'secreto-de-prueba']);
    $this->actingAs($admin);

    expect(User::count())->toBeGreaterThanOrEqual(3);
});

test('sin usuario autenticado el scope no filtra nada', function () {
    $clienteA = Cliente::factory()->create();
    $clienteB = Cliente::factory()->create();
    User::factory()->cliente()->create(['cliente_id' => $clienteA->id]);
    User::factory()->cliente()->create(['cliente_id' => $clienteB->id]);

    expect(User::count())->toBe(2);
});
