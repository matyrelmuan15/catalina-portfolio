<?php

use App\Models\Cliente;
use App\Models\User;

it('cierra la sesión y vuelve al portfolio', function () {
    $cliente = Cliente::factory()->create();
    $usuario = User::factory()->for($cliente)->create();

    $this->actingAs($usuario);

    $this->post(route('salir'))->assertRedirect(route('portfolio.inicio'));

    expect(auth()->check())->toBeFalse();
});

it('exige sesión para cerrar sesión', function () {
    $this->post(route('salir'))->assertRedirect(route('ingresar'));
});
