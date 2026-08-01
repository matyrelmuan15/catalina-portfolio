<?php

use App\Models\Cliente;
use App\Models\User;
use App\Scopes\PerteneceAlCliente;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

it('filtra por el cliente_id del usuario autenticado cuando es cliente', function () {
    $cliente = Cliente::factory()->create();
    $usuario = User::factory()->for($cliente)->create();

    $this->actingAs($usuario);

    $builder = Mockery::mock(Builder::class);
    $builder->shouldReceive('where')->once()->with('agenda_eventos.cliente_id', $usuario->cliente_id);

    $modelo = Mockery::mock(Model::class);
    $modelo->shouldReceive('qualifyColumn')->with('cliente_id')->andReturn('agenda_eventos.cliente_id');

    (new PerteneceAlCliente)->apply($builder, $modelo);
});

it('no agrega ningún filtro cuando el usuario autenticado es admin', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $builder = Mockery::mock(Builder::class);
    $builder->shouldNotReceive('where');

    $modelo = Mockery::mock(Model::class);

    (new PerteneceAlCliente)->apply($builder, $modelo);
});

it('no agrega ningún filtro cuando no hay usuario autenticado', function () {
    $builder = Mockery::mock(Builder::class);
    $builder->shouldNotReceive('where');

    $modelo = Mockery::mock(Model::class);

    (new PerteneceAlCliente)->apply($builder, $modelo);
});
