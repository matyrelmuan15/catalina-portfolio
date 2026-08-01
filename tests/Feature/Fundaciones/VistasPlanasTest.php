<?php

use App\Models\Cliente;
use App\Models\User;

/**
 * Las páginas declaradas con `Route::view` (las secciones "próximamente" del
 * panel y del portal) no pasan por ningún componente Livewire, así que son las
 * únicas que renderizan el layout como componente Blade. Hasta que se agregó
 * esta prueba, ninguna otra llegaba a renderizarlas: la batería de aislamiento
 * solo verifica redirecciones y 403, nunca un 200. Las siete devolvían 500 por
 * un componente mal invocado y nadie se enteraba.
 */
it('renderiza cada sección del panel declarada con Route::view', function (string $ruta) {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get($ruta)->assertOk();
})->with([
    '/panel/clientes',
    '/panel/pedidos',
    '/panel/cuenta',
]);

it('renderiza cada sección del portal declarada con Route::view', function (string $ruta) {
    $usuario = User::factory()->for(Cliente::factory())->create();

    $this->actingAs($usuario)->get($ruta)->assertOk();
})->with([
    '/portal/calendario',
    '/portal/publicaciones',
    '/portal/pedidos',
    '/portal/metricas',
]);
