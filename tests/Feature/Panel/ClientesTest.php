<?php

use App\Livewire\Auth\Ingreso;
use App\Livewire\Panel\Clientes\Ficha;
use App\Livewire\Panel\Clientes\Formulario;
use App\Livewire\Panel\Clientes\Listado;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

function crearAdministradoraDePrueba(): User
{
    return User::factory()->admin()->create(['two_factor_secret' => 'secreto-de-prueba']);
}

test('un cliente no puede ver el listado de clientes del panel', function () {
    $cliente = Cliente::factory()->create();
    $usuario = User::factory()->cliente()->create(['cliente_id' => $cliente->id]);

    $this->actingAs($usuario)->get('/panel/clientes')->assertForbidden();
});

test('el listado solo muestra clientes activos por defecto', function () {
    Cliente::factory()->count(2)->create(['archivado' => false]);
    Cliente::factory()->create(['archivado' => true]);

    Livewire::actingAs(crearAdministradoraDePrueba())
        ->test(Listado::class)
        ->assertViewHas('clientes', fn ($clientes) => $clientes->count() === 2)
        ->set('verArchivados', true)
        ->assertViewHas('clientes', fn ($clientes) => $clientes->count() === 3);
});

test('el alta crea el cliente y su usuario en una sola operacion', function () {
    Livewire::actingAs(crearAdministradoraDePrueba())
        ->test(Formulario::class)
        ->set('marca', 'Estudio Lume')
        ->set('contacto', 'Ana Gómez')
        ->set('correo', 'ana@estudiolume.test')
        ->set('color', 'coral')
        ->call('guardar')
        ->assertSet('claveGenerada', fn ($clave) => is_string($clave) && strlen($clave) >= 14);

    $cliente = Cliente::firstWhere('marca', 'Estudio Lume');
    expect($cliente)->not->toBeNull();

    $usuario = User::firstWhere('email', 'ana@estudiolume.test');
    expect($usuario)->not->toBeNull();
    expect($usuario->cliente_id)->toBe($cliente->id);
    expect($usuario->rol)->toBe('cliente');
});

test('el alta con un correo repetido falla y no deja registros a medias', function () {
    $existente = User::factory()->cliente()->create(['email' => 'repetido@catalina.test']);
    $cantidadDeClientesAntes = Cliente::count();

    Livewire::actingAs(crearAdministradoraDePrueba())
        ->test(Formulario::class)
        ->set('marca', 'Marca Nueva')
        ->set('contacto', 'Alguien')
        ->set('correo', 'repetido@catalina.test')
        ->set('color', 'fucsia')
        ->call('guardar')
        ->assertHasErrors('correo');

    expect(Cliente::count())->toBe($cantidadDeClientesAntes);
});

test('archivar un cliente le impide iniciar sesion', function () {
    $cliente = Cliente::factory()->create(['archivado' => false]);
    $usuarioCliente = User::factory()->cliente()->create(['cliente_id' => $cliente->id, 'password' => Hash::make('una-clave-larga-1')]);

    Livewire::actingAs(crearAdministradoraDePrueba())
        ->test(Listado::class)
        ->call('archivar', $cliente->id);

    expect($cliente->fresh()->archivado)->toBeTrue();

    auth()->logout();

    Livewire::test(Ingreso::class)
        ->set('correo', $usuarioCliente->email)
        ->set('clave', 'una-clave-larga-1')
        ->call('ingresar')
        ->assertHasErrors('ingreso');

    $this->assertGuest();
});

test('desarchivar devuelve el cliente a la lista de activos', function () {
    $cliente = Cliente::factory()->create(['archivado' => true]);

    Livewire::actingAs(crearAdministradoraDePrueba())
        ->test(Listado::class)
        ->call('desarchivar', $cliente->id);

    expect($cliente->fresh()->archivado)->toBeFalse();
});

test('restablecer la clave desde la ficha genera una clave nueva que funciona', function () {
    $cliente = Cliente::factory()->create();
    $usuarioCliente = User::factory()->cliente()->create(['cliente_id' => $cliente->id, 'password' => Hash::make('clave-vieja-123')]);

    $componente = Livewire::actingAs(crearAdministradoraDePrueba())
        ->test(Ficha::class, ['cliente' => $cliente])
        ->call('restablecerClave');

    $claveNueva = $componente->get('claveGenerada');
    expect($claveNueva)->not->toBeNull();
    expect(Hash::check($claveNueva, $usuarioCliente->fresh()->password))->toBeTrue();
});

test('un cliente no puede administrar clientes aunque conozca el id de otra marca', function () {
    $clienteA = Cliente::factory()->create();
    $clienteB = Cliente::factory()->create();
    $usuarioA = User::factory()->cliente()->create(['cliente_id' => $clienteA->id]);

    $this->actingAs($usuarioA)->get('/panel/clientes/'.$clienteB->id)->assertForbidden();
});
