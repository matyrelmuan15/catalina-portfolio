<?php

use App\Livewire\Auth\Ingreso;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

it('deja entrar a un cliente con las credenciales correctas', function () {
    $cliente = Cliente::factory()->create();
    $usuario = User::factory()->for($cliente)->create([
        'email' => 'julieta@bloomskincare.com.ar',
        'password' => Hash::make('ClientePrueba2026'),
    ]);

    Livewire::test(Ingreso::class)
        ->set('correo', 'julieta@bloomskincare.com.ar')
        ->set('clave', 'ClientePrueba2026')
        ->set('turnstileToken', 'token-de-prueba')
        ->call('ingresar')
        ->assertRedirect(route('portal.inicio'));

    expect(auth()->check())->toBeTrue();
    expect(auth()->user()->id)->toBe($usuario->id);
});

it('manda a la administradora al desafío del segundo factor si ya lo tiene activo', function () {
    $admin = User::factory()->admin()->create([
        'email' => 'catalinaavendanio@gmail.com',
        'password' => Hash::make('AdminCatalina2026'),
        'two_factor_secret' => 'SECRETODEPRUEBA',
    ]);

    Livewire::test(Ingreso::class)
        ->set('correo', 'catalinaavendanio@gmail.com')
        ->set('clave', 'AdminCatalina2026')
        ->set('turnstileToken', 'token-de-prueba')
        ->call('ingresar')
        ->assertRedirect(route('panel.segundo-factor'));

    expect(auth()->check())->toBeFalse();
    expect(session('2fa.pendiente'))->toBe($admin->id);
});

it('manda a la administradora a configurar el segundo factor la primera vez', function () {
    User::factory()->admin()->create([
        'email' => 'catalinaavendanio@gmail.com',
        'password' => Hash::make('AdminCatalina2026'),
        'two_factor_secret' => null,
    ]);

    Livewire::test(Ingreso::class)
        ->set('correo', 'catalinaavendanio@gmail.com')
        ->set('clave', 'AdminCatalina2026')
        ->set('turnstileToken', 'token-de-prueba')
        ->call('ingresar')
        ->assertRedirect(route('panel.segundo-factor.configurar'));
});

it('rechaza una clave incorrecta con un mensaje genérico', function () {
    $cliente = Cliente::factory()->create();
    User::factory()->for($cliente)->create([
        'email' => 'julieta@bloomskincare.com.ar',
        'password' => Hash::make('ClientePrueba2026'),
    ]);

    Livewire::test(Ingreso::class)
        ->set('correo', 'julieta@bloomskincare.com.ar')
        ->set('clave', 'clave-incorrecta')
        ->set('turnstileToken', 'token-de-prueba')
        ->call('ingresar')
        ->assertSet('error', 'El correo o la clave no coinciden.');

    expect(auth()->check())->toBeFalse();
});

it('rechaza un correo que no existe con el mismo mensaje genérico', function () {
    Livewire::test(Ingreso::class)
        ->set('correo', 'no-existe@marca.com')
        ->set('clave', 'cualquier-clave')
        ->set('turnstileToken', 'token-de-prueba')
        ->call('ingresar')
        ->assertSet('error', 'El correo o la clave no coinciden.');
});

it('no deja entrar a un cliente cuya marca está archivada', function () {
    $cliente = Cliente::factory()->archivado()->create();
    User::factory()->for($cliente)->create([
        'email' => 'archivada@marca.com',
        'password' => Hash::make('ClientePrueba2026'),
    ]);

    Livewire::test(Ingreso::class)
        ->set('correo', 'archivada@marca.com')
        ->set('clave', 'ClientePrueba2026')
        ->set('turnstileToken', 'token-de-prueba')
        ->call('ingresar')
        ->assertSet('error', 'Tu acceso está desactivado. Consultá con Catalina.');

    expect(auth()->check())->toBeFalse();
});

it('no deja entrar a un usuario inactivo aunque la clave sea correcta', function () {
    $cliente = Cliente::factory()->create();
    User::factory()->for($cliente)->inactivo()->create([
        'email' => 'inactivo@marca.com',
        'password' => Hash::make('ClientePrueba2026'),
    ]);

    Livewire::test(Ingreso::class)
        ->set('correo', 'inactivo@marca.com')
        ->set('clave', 'ClientePrueba2026')
        ->set('turnstileToken', 'token-de-prueba')
        ->call('ingresar');

    expect(auth()->check())->toBeFalse();
});

it('bloquea después de seis intentos fallidos en un minuto', function () {
    $cliente = Cliente::factory()->create();
    User::factory()->for($cliente)->create([
        'email' => 'julieta@bloomskincare.com.ar',
        'password' => Hash::make('ClientePrueba2026'),
    ]);

    for ($i = 0; $i < 5; $i++) {
        Livewire::test(Ingreso::class)
            ->set('correo', 'julieta@bloomskincare.com.ar')
            ->set('clave', 'clave-incorrecta')
            ->set('turnstileToken', 'token-de-prueba')
            ->call('ingresar');
    }

    Livewire::test(Ingreso::class)
        ->set('correo', 'julieta@bloomskincare.com.ar')
        ->set('clave', 'ClientePrueba2026')
        ->set('turnstileToken', 'token-de-prueba')
        ->call('ingresar')
        ->assertSee('Demasiados intentos', false);

    expect(auth()->check())->toBeFalse();

    RateLimiter::clear('ingreso|julieta@bloomskincare.com.ar|127.0.0.1');
});

it('manda a la administradora que ya tiene sesión directo al panel si visita /ingresar', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get('/ingresar')->assertRedirect(route('panel.inicio'));
});

it('manda al cliente que ya tiene sesión directo al portal si visita /ingresar', function () {
    $cliente = Cliente::factory()->create();
    $usuario = User::factory()->for($cliente)->create();

    $this->actingAs($usuario)->get('/ingresar')->assertRedirect(route('portal.inicio'));
});

it('rechaza el ingreso si Turnstile no confirma al visitante', function () {
    $cliente = Cliente::factory()->create();
    User::factory()->for($cliente)->create([
        'email' => 'julieta@bloomskincare.com.ar',
        'password' => Hash::make('ClientePrueba2026'),
    ]);

    Livewire::test(Ingreso::class)
        ->set('correo', 'julieta@bloomskincare.com.ar')
        ->set('clave', 'ClientePrueba2026')
        ->set('turnstileToken', 'token-rechazado')
        ->call('ingresar')
        ->assertHasErrors('turnstileToken');

    expect(auth()->check())->toBeFalse();
});
