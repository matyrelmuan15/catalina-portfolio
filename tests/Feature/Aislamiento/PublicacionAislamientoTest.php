<?php

use App\Livewire\Portal\Publicaciones;
use App\Models\Cliente;
use App\Models\Publicacion;
use App\Models\User;

/**
 * publicaciones es el tercer modelo con cliente_id. Misma batería que
 * agenda_eventos y users, ver docs/02-arquitectura-y-datos.md §6.1.
 */
test('un cliente autenticado solo ve sus propias publicaciones', function () {
    $clienteA = Cliente::factory()->create();
    $clienteB = Cliente::factory()->create();

    Publicacion::factory()->create(['cliente_id' => $clienteA->id]);
    Publicacion::factory()->create(['cliente_id' => $clienteB->id]);

    $usuarioA = User::factory()->cliente()->create(['cliente_id' => $clienteA->id]);
    $this->actingAs($usuarioA);

    expect(Publicacion::count())->toBe(1);
});

test('un cliente no puede recuperar por id una publicacion de otro cliente', function () {
    $clienteA = Cliente::factory()->create();
    $clienteB = Cliente::factory()->create();

    $publicacionB = Publicacion::factory()->create(['cliente_id' => $clienteB->id]);
    $usuarioA = User::factory()->cliente()->create(['cliente_id' => $clienteA->id]);

    $this->actingAs($usuarioA);

    expect(Publicacion::find($publicacionB->id))->toBeNull();
});

test('la ruta de publicaciones del portal de un cliente no expone datos de otro', function () {
    $clienteA = Cliente::factory()->create();
    $clienteB = Cliente::factory()->create();

    Publicacion::factory()->create(['cliente_id' => $clienteA->id, 'titulo' => 'Pieza de A']);
    Publicacion::factory()->create(['cliente_id' => $clienteB->id, 'titulo' => 'Pieza de B']);

    $usuarioA = User::factory()->cliente()->create(['cliente_id' => $clienteA->id]);

    $respuesta = $this->actingAs($usuarioA)->get('/portal/publicaciones');

    $respuesta->assertSee('Pieza de A');
    $respuesta->assertDontSee('Pieza de B');
});

test('el portal del cliente no tiene ningun camino para crear, editar o eliminar publicaciones', function () {
    expect(method_exists(Publicaciones::class, 'guardar'))->toBeFalse();
    expect(method_exists(Publicaciones::class, 'eliminar'))->toBeFalse();
});

test('el cliente no ve las columnas internas de publicaciones', function () {
    $cliente = Cliente::factory()->create();
    Publicacion::factory()->create([
        'cliente_id' => $cliente->id,
        'id_media' => '17912345678901234',
        'copy_texto' => 'Copy secreto de Meta',
        'hashtags' => '#interno',
    ]);
    $usuario = User::factory()->cliente()->create(['cliente_id' => $cliente->id]);

    $respuesta = $this->actingAs($usuario)->get('/portal/publicaciones');

    $respuesta->assertDontSee('17912345678901234');
    $respuesta->assertDontSee('Copy secreto de Meta');
    $respuesta->assertDontSee('#interno');
});
