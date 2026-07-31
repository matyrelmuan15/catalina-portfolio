<?php

use App\Livewire\Panel\Clientes\Calendario;
use App\Livewire\Portal\Calendario as PortalCalendario;
use App\Models\AgendaEvento;
use App\Models\Cliente;
use App\Models\Publicacion;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('la administradora carga un evento y aparece en el calendario del cliente', function () {
    $cliente = Cliente::factory()->create();
    $admin = User::factory()->admin()->create(['two_factor_secret' => 'secreto-de-prueba']);

    Livewire::actingAs($admin)
        ->test(Calendario::class, ['cliente' => $cliente])
        ->call('abrirAlta', today()->toDateString())
        ->set('tipo', 'grabacion')
        ->set('titulo', 'Sesión de producto')
        ->set('nota', 'Traer todo listo')
        ->call('guardar');

    $evento = AgendaEvento::firstWhere('titulo', 'Sesión de producto');
    expect($evento)->not->toBeNull();
    expect($evento->cliente_id)->toBe($cliente->id);

    $usuarioCliente = User::factory()->cliente()->create(['cliente_id' => $cliente->id]);

    Livewire::actingAs($usuarioCliente)
        ->test(PortalCalendario::class)
        ->assertSee('Sesión de producto');
});

test('editar un evento actualiza sus datos', function () {
    $cliente = Cliente::factory()->create();
    $evento = AgendaEvento::factory()->create(['cliente_id' => $cliente->id, 'titulo' => 'Título viejo']);
    $admin = User::factory()->admin()->create(['two_factor_secret' => 'secreto-de-prueba']);

    Livewire::actingAs($admin)
        ->test(Calendario::class, ['cliente' => $cliente])
        ->call('abrirEdicion', $evento->id)
        ->set('titulo', 'Título nuevo')
        ->call('guardar');

    expect($evento->fresh()->titulo)->toBe('Título nuevo');
});

test('eliminar un evento lo borra', function () {
    $cliente = Cliente::factory()->create();
    $evento = AgendaEvento::factory()->create(['cliente_id' => $cliente->id]);
    $admin = User::factory()->admin()->create(['two_factor_secret' => 'secreto-de-prueba']);

    Livewire::actingAs($admin)
        ->test(Calendario::class, ['cliente' => $cliente])
        ->call('eliminar', $evento->id);

    expect(AgendaEvento::find($evento->id))->toBeNull();
});

test('la administradora no puede cargar un evento en un cliente que no es el de la ficha', function () {
    $clienteA = Cliente::factory()->create();
    $clienteB = Cliente::factory()->create();
    $eventoDeB = AgendaEvento::factory()->create(['cliente_id' => $clienteB->id]);
    $admin = User::factory()->admin()->create(['two_factor_secret' => 'secreto-de-prueba']);

    Livewire::actingAs($admin)
        ->test(Calendario::class, ['cliente' => $clienteA])
        ->call('abrirEdicion', $eventoDeB->id);
})->throws(ModelNotFoundException::class);

test('las publicaciones aparecen en el calendario del cliente en su fecha, sin ser un evento de agenda', function () {
    $cliente = Cliente::factory()->create();
    $publicacion = Publicacion::factory()->create([
        'cliente_id' => $cliente->id,
        'titulo' => 'Reel de lanzamiento',
        'fecha' => today()->toDateString(),
    ]);
    $usuarioCliente = User::factory()->cliente()->create(['cliente_id' => $cliente->id]);

    Livewire::actingAs($usuarioCliente)
        ->test(PortalCalendario::class)
        ->assertSee('Reel de lanzamiento')
        ->assertSee('Publicación');

    expect(AgendaEvento::where('titulo', 'Reel de lanzamiento')->exists())->toBeFalse();
});

test('el calendario respeta la zona horaria de Argentina', function () {
    expect(config('app.timezone'))->toBe('America/Argentina/Buenos_Aires');
});
