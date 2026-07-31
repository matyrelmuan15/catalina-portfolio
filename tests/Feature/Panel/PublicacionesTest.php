<?php

use App\Livewire\Panel\Publicaciones\Formulario;
use App\Livewire\Panel\Publicaciones\Listado;
use App\Models\Cliente;
use App\Models\Publicacion;
use App\Models\PublicacionMetrica;
use App\Models\User;
use Livewire\Livewire;

function crearAdministradoraParaPublicaciones(): User
{
    return User::factory()->admin()->create(['two_factor_secret' => 'secreto-de-prueba']);
}

test('un cliente no puede ver el listado de publicaciones del panel', function () {
    $cliente = Cliente::factory()->create();
    $usuario = User::factory()->cliente()->create(['cliente_id' => $cliente->id]);

    $this->actingAs($usuario)->get('/panel/publicaciones')->assertForbidden();
});

test('cargar una publicacion nueva la crea en estado planificada', function () {
    $cliente = Cliente::factory()->create();

    Livewire::actingAs(crearAdministradoraParaPublicaciones())
        ->test(Formulario::class)
        ->set('cliente_id', $cliente->id)
        ->set('titulo', 'Reel de lanzamiento')
        ->set('estado', 'Planificada')
        ->set('fecha', today()->toDateString())
        ->set('plataforma', 'instagram')
        ->set('formato', 'reel')
        ->call('guardar')
        ->assertDispatched('publicacion-guardada');

    $publicacion = Publicacion::firstWhere('titulo', 'Reel de lanzamiento');
    expect($publicacion)->not->toBeNull();
    expect($publicacion->estado)->toBe('Planificada');
    expect($publicacion->cliente_id)->toBe($cliente->id);
});

test('una publicacion medida no retrocede de estado al editarla', function () {
    $cliente = Cliente::factory()->create();
    $publicacion = Publicacion::factory()->create(['cliente_id' => $cliente->id, 'estado' => 'Medida']);

    Livewire::actingAs(crearAdministradoraParaPublicaciones())
        ->test(Formulario::class, ['publicacionId' => $publicacion->id])
        ->set('titulo', 'Título editado')
        ->set('estado', 'Planificada')
        ->call('guardar');

    expect($publicacion->fresh()->estado)->toBe('Medida');
    expect($publicacion->fresh()->titulo)->toBe('Título editado');
});

test('el listado global no queda restringido a ningun cliente', function () {
    $clienteA = Cliente::factory()->create();
    $clienteB = Cliente::factory()->create();
    Publicacion::factory()->create(['cliente_id' => $clienteA->id]);
    Publicacion::factory()->create(['cliente_id' => $clienteB->id]);

    Livewire::actingAs(crearAdministradoraParaPublicaciones())
        ->test(Listado::class)
        ->assertViewHas('publicaciones', fn ($publicaciones) => $publicaciones->count() === 2);
});

test('el listado filtra por cliente cuando se abre desde su ficha', function () {
    $clienteA = Cliente::factory()->create();
    $clienteB = Cliente::factory()->create();
    Publicacion::factory()->create(['cliente_id' => $clienteA->id]);
    Publicacion::factory()->create(['cliente_id' => $clienteB->id]);

    Livewire::actingAs(crearAdministradoraParaPublicaciones())
        ->test(Listado::class, ['cliente' => $clienteA])
        ->assertViewHas('publicaciones', fn ($publicaciones) => $publicaciones->count() === 1);
});

test('eliminar una publicacion la borra en forma definitiva', function () {
    $publicacion = Publicacion::factory()->create();

    Livewire::actingAs(crearAdministradoraParaPublicaciones())
        ->test(Listado::class)
        ->call('eliminar', $publicacion->id);

    expect(Publicacion::find($publicacion->id))->toBeNull();
});

test('la tasa de interaccion se calcula sola cuando no viene en el archivo', function () {
    $metrica = PublicacionMetrica::factory()->create([
        'alcance' => 20000,
        'interacciones' => 1500,
        'tasa_interaccion' => null,
    ]);

    expect((float) $metrica->tasa_interaccion)->toBe(7.5);
});

test('la tasa de interaccion no se calcula si no hay alcance', function () {
    $metrica = PublicacionMetrica::factory()->create([
        'alcance' => null,
        'interacciones' => null,
        'tasa_interaccion' => null,
    ]);

    expect($metrica->tasa_interaccion)->toBeNull();
});

test('la tabla se usa sin trabas con 200 publicaciones cargadas', function () {
    Publicacion::factory()->count(200)->create();

    $componente = Livewire::actingAs(crearAdministradoraParaPublicaciones())
        ->test(Listado::class)
        ->assertOk();

    expect($componente->viewData('publicaciones')->count())->toBe(200);
});
