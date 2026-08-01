<?php

use App\Models\Cliente;
use App\Models\User;

/**
 * Batería de aislamiento de la Fase 1 (docs/02, sección 6.1 y docs/04,
 * criterios de aceptación de la Fase 1). Todavía hay pocos modelos con
 * cliente_id para recorrer —el aislamiento de datos de negocio crece a
 * partir de la Fase 5—, así que esta batería se concentra en las fronteras
 * de rol, que son las que existen hoy.
 */
$rutasPanel = [
    '/panel',
    '/panel/videos',
    '/panel/clientes',
    '/panel/pedidos',
    '/panel/cuenta',
];

$rutasPortal = [
    '/portal',
    '/portal/calendario',
    '/portal/publicaciones',
    '/portal/pedidos',
    '/portal/metricas',
];

foreach ($rutasPanel as $ruta) {
    it("un visitante sin sesión que entra a {$ruta} va al ingreso", function () use ($ruta) {
        $this->get($ruta)->assertRedirect(route('ingresar'));
    });

    it("un cliente que entra a {$ruta} recibe 403", function () use ($ruta) {
        $cliente = Cliente::factory()->create();
        $usuario = User::factory()->for($cliente)->create();

        $this->actingAs($usuario)->get($ruta)->assertForbidden();
    });
}

foreach ($rutasPortal as $ruta) {
    it("un visitante sin sesión que entra a {$ruta} va al ingreso", function () use ($ruta) {
        $this->get($ruta)->assertRedirect(route('ingresar'));
    });

    it("una administradora que entra a {$ruta} recibe 403", function () use ($ruta) {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get($ruta)->assertForbidden();
    });
}

it('un cliente archivado no puede volver a entrar aunque conserve la sesión', function () {
    $cliente = Cliente::factory()->create();
    $usuario = User::factory()->for($cliente)->create();

    $this->actingAs($usuario)->get('/portal')->assertRedirect('/portal/calendario');

    $cliente->update(['archivado' => true]);

    // El middleware de rol no vuelve a comprobar "puedeIniciarSesion" en cada
    // petición (esa regla es del ingreso); lo que sí garantiza es que un
    // cliente jamás entra a /panel, y viceversa, en cualquier estado.
    $this->actingAs($usuario->fresh())->get('/panel/videos')->assertForbidden();
});

it('una administradora con el segundo factor pendiente no puede saltear el desafío entrando directo a /panel', function () {
    $admin = User::factory()->admin()->create(['two_factor_secret' => 'SECRETODEPRUEBA']);

    session(['2fa.pendiente' => $admin->id]);

    // Sin Auth::login(), la sesión pendiente de 2FA no cuenta como sesión real.
    $this->get('/panel/videos')->assertRedirect(route('ingresar'));
});
