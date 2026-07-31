<?php

use App\Livewire\Portal\Calendario;
use App\Models\AgendaEvento;
use App\Models\Cliente;
use App\Models\User;

/**
 * agenda_eventos es el segundo modelo con cliente_id después de users
 * (fase 1). Suma su propia prueba de aislamiento en el mismo commit que lo
 * agrega, como exige docs/04-plan-de-fases.md.
 */
test('un cliente autenticado solo ve sus propios eventos de agenda', function () {
    $clienteA = Cliente::factory()->create();
    $clienteB = Cliente::factory()->create();

    AgendaEvento::factory()->create(['cliente_id' => $clienteA->id]);
    AgendaEvento::factory()->create(['cliente_id' => $clienteB->id]);

    $usuarioA = User::factory()->cliente()->create(['cliente_id' => $clienteA->id]);
    $this->actingAs($usuarioA);

    expect(AgendaEvento::count())->toBe(1);
});

test('un cliente no puede recuperar por id un evento de otro cliente', function () {
    $clienteA = Cliente::factory()->create();
    $clienteB = Cliente::factory()->create();

    $eventoB = AgendaEvento::factory()->create(['cliente_id' => $clienteB->id]);
    $usuarioA = User::factory()->cliente()->create(['cliente_id' => $clienteA->id]);

    $this->actingAs($usuarioA);

    expect(AgendaEvento::find($eventoB->id))->toBeNull();
});

test('crear un evento de agenda estando autenticado como cliente le asigna su propio cliente_id', function () {
    $cliente = Cliente::factory()->create();
    $usuario = User::factory()->cliente()->create(['cliente_id' => $cliente->id]);

    $this->actingAs($usuario);

    $evento = AgendaEvento::create(['fecha' => today(), 'tipo' => 'grabacion', 'titulo' => 'Prueba']);

    expect($evento->cliente_id)->toBe($cliente->id);
});

test('el portal del cliente no tiene ningun camino para crear, editar o eliminar eventos', function () {
    expect(method_exists(Calendario::class, 'guardar'))->toBeFalse();
    expect(method_exists(Calendario::class, 'eliminar'))->toBeFalse();
    expect(method_exists(Calendario::class, 'abrirAlta'))->toBeFalse();
});
