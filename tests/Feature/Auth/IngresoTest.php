<?php

use App\Livewire\Auth\Ingreso;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

test('un administrador sin segundo factor ingresa y va directo al panel', function () {
    $admin = User::factory()->admin()->create(['password' => Hash::make('una-clave-larga-1')]);

    Livewire::test(Ingreso::class)
        ->set('correo', $admin->email)
        ->set('clave', 'una-clave-larga-1')
        ->call('ingresar')
        ->assertRedirect(route('panel.inicio'));

    $this->assertAuthenticatedAs($admin);
});

test('un administrador con segundo factor activo no entra hasta verificar el código', function () {
    $admin = User::factory()->admin()->create([
        'password' => Hash::make('una-clave-larga-1'),
        'two_factor_secret' => 'secreto-de-prueba',
    ]);

    Livewire::test(Ingreso::class)
        ->set('correo', $admin->email)
        ->set('clave', 'una-clave-larga-1')
        ->call('ingresar')
        ->assertRedirect(route('ingresar.verificar'));

    $this->assertGuest();
});

test('un cliente ingresa y va directo al portal', function () {
    $cliente = Cliente::factory()->create();
    $usuario = User::factory()->cliente()->create([
        'cliente_id' => $cliente->id,
        'password' => Hash::make('una-clave-larga-1'),
    ]);

    Livewire::test(Ingreso::class)
        ->set('correo', $usuario->email)
        ->set('clave', 'una-clave-larga-1')
        ->call('ingresar')
        ->assertRedirect(route('portal.inicio'));

    $this->assertAuthenticatedAs($usuario);
});

test('la clave incorrecta no ingresa y muestra un error', function () {
    $usuario = User::factory()->cliente()->create(['password' => Hash::make('una-clave-larga-1')]);

    Livewire::test(Ingreso::class)
        ->set('correo', $usuario->email)
        ->set('clave', 'clave-equivocada')
        ->call('ingresar')
        ->assertHasErrors('ingreso');

    $this->assertGuest();
});

test('un cliente archivado no puede ingresar aunque la clave sea correcta', function () {
    $cliente = Cliente::factory()->archivado()->create();
    $usuario = User::factory()->cliente()->create([
        'cliente_id' => $cliente->id,
        'password' => Hash::make('una-clave-larga-1'),
    ]);

    Livewire::test(Ingreso::class)
        ->set('correo', $usuario->email)
        ->set('clave', 'una-clave-larga-1')
        ->call('ingresar')
        ->assertHasErrors('ingreso');

    $this->assertGuest();
});

test('seis intentos fallidos en un minuto quedan bloqueados', function () {
    $usuario = User::factory()->cliente()->create(['password' => Hash::make('una-clave-larga-1')]);

    for ($intento = 1; $intento <= 5; $intento++) {
        Livewire::test(Ingreso::class)
            ->set('correo', $usuario->email)
            ->set('clave', 'clave-equivocada')
            ->call('ingresar')
            ->assertHasErrors('ingreso');
    }

    expect(RateLimiter::tooManyAttempts('ingreso:127.0.0.1', 5))->toBeTrue();

    // El sexto intento, aunque la clave ahora sea la correcta, queda bloqueado.
    Livewire::test(Ingreso::class)
        ->set('correo', $usuario->email)
        ->set('clave', 'una-clave-larga-1')
        ->call('ingresar')
        ->assertHasErrors('ingreso');

    $this->assertGuest();
});
