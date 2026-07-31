<?php

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Support\Facades\Route;

test('un cliente que entra al panel recibe 403', function () {
    $cliente = Cliente::factory()->create();
    $usuario = User::factory()->cliente()->create(['cliente_id' => $cliente->id]);

    $this->actingAs($usuario)->get('/panel/videos')->assertForbidden();
});

test('un visitante sin sesión que entra al portal va al ingreso', function () {
    $this->get('/portal/calendario')->assertRedirect(route('ingresar'));
});

test('un visitante sin sesión que entra al panel va al ingreso', function () {
    $this->get('/panel/videos')->assertRedirect(route('ingresar'));
});

test('un administrador que entra al portal recibe 403', function () {
    $admin = User::factory()->admin()->create(['two_factor_secret' => 'secreto-de-prueba']);

    $this->actingAs($admin)->get('/portal/calendario')->assertForbidden();
});

test('un cliente archivado no puede usar el portal aunque tenga sesión activa', function () {
    $cliente = Cliente::factory()->archivado()->create();
    $usuario = User::factory()->cliente()->create(['cliente_id' => $cliente->id]);

    $this->actingAs($usuario)->get('/portal/calendario')->assertForbidden();
});

test('un usuario inactivo no puede usar el panel ni el portal', function () {
    $admin = User::factory()->admin()->inactivo()->create();
    $this->actingAs($admin)->get('/panel/videos')->assertForbidden();

    $cliente = Cliente::factory()->create();
    $usuarioCliente = User::factory()->cliente()->inactivo()->create(['cliente_id' => $cliente->id]);
    $this->actingAs($usuarioCliente)->get('/portal/calendario')->assertForbidden();
});

test('ninguna ruta del portal acepta un cliente_id por parámetro', function () {
    $rutas = collect(Route::getRoutes())
        ->filter(fn ($ruta) => str_starts_with($ruta->uri(), 'portal'));

    foreach ($rutas as $ruta) {
        expect($ruta->uri())->not->toContain('cliente');
    }
});
