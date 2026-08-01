<?php

use App\Models\Cliente;
use App\Models\User;
use App\Policies\UserPolicy;

it('deja a la administradora ver y editar la cuenta de cualquier usuario', function () {
    $admin = User::factory()->admin()->create();
    $cliente = Cliente::factory()->create();
    $otro = User::factory()->for($cliente)->create();

    $politica = new UserPolicy;

    expect($politica->view($admin, $otro))->toBeTrue();
    expect($politica->update($admin, $otro))->toBeTrue();
});

it('deja a un cliente ver y editar su propia cuenta', function () {
    $cliente = Cliente::factory()->create();
    $usuario = User::factory()->for($cliente)->create();

    $politica = new UserPolicy;

    expect($politica->view($usuario, $usuario))->toBeTrue();
    expect($politica->update($usuario, $usuario))->toBeTrue();
});

it('un cliente no puede ver ni editar la cuenta de otro cliente', function () {
    $clienteA = Cliente::factory()->create();
    $clienteB = Cliente::factory()->create();
    $usuarioA = User::factory()->for($clienteA)->create();
    $usuarioB = User::factory()->for($clienteB)->create();

    $politica = new UserPolicy;

    expect($politica->view($usuarioA, $usuarioB))->toBeFalse();
    expect($politica->update($usuarioA, $usuarioB))->toBeFalse();
});
