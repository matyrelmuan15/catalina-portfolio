<?php

use App\Livewire\Auth\RestablecerClave;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;

it('restablece la clave con un token válido e invalida las sesiones anteriores', function () {
    $cliente = Cliente::factory()->create();
    $usuario = User::factory()->for($cliente)->create([
        'email' => 'julieta@bloomskincare.com.ar',
        'password' => Hash::make('ClaveVieja2026'),
    ]);

    DB::table('sessions')->insert([
        'id' => 'sesion-anterior',
        'user_id' => $usuario->id,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'prueba',
        'payload' => base64_encode('a:0:{}'),
        'last_activity' => now()->timestamp,
    ]);

    $token = Password::broker()->createToken($usuario);

    Livewire::test(RestablecerClave::class, ['token' => $token])
        ->set('correo', 'julieta@bloomskincare.com.ar')
        ->set('clave', 'ClaveNuevaSegura2026')
        ->set('clave_confirmation', 'ClaveNuevaSegura2026')
        ->call('restablecer')
        ->assertSet('listo', true);

    expect(Hash::check('ClaveNuevaSegura2026', $usuario->fresh()->password))->toBeTrue();
    expect(DB::table('sessions')->where('id', 'sesion-anterior')->exists())->toBeFalse();
});

it('rechaza un token vencido o inválido', function () {
    $cliente = Cliente::factory()->create();
    $usuario = User::factory()->for($cliente)->create(['email' => 'julieta@bloomskincare.com.ar']);

    Livewire::test(RestablecerClave::class, ['token' => 'token-invalido'])
        ->set('correo', 'julieta@bloomskincare.com.ar')
        ->set('clave', 'ClaveNuevaSegura2026')
        ->set('clave_confirmation', 'ClaveNuevaSegura2026')
        ->call('restablecer')
        ->assertSet('error', 'El enlace no es válido o ya venció. Pedí uno nuevo.');
});

it('exige una clave de al menos 10 caracteres', function () {
    $cliente = Cliente::factory()->create();
    $usuario = User::factory()->for($cliente)->create(['email' => 'julieta@bloomskincare.com.ar']);
    $token = Password::broker()->createToken($usuario);

    Livewire::test(RestablecerClave::class, ['token' => $token])
        ->set('correo', 'julieta@bloomskincare.com.ar')
        ->set('clave', 'corta1')
        ->set('clave_confirmation', 'corta1')
        ->call('restablecer')
        ->assertHasErrors('clave');
});
