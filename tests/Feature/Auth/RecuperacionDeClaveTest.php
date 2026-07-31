<?php

use App\Livewire\Auth\RecuperarClave;
use App\Livewire\Auth\RestablecerClave;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;

test('pedir el enlace de recuperación encola la notificación', function () {
    Notification::fake();
    $usuario = User::factory()->cliente()->create();

    Livewire::test(RecuperarClave::class)
        ->set('correo', $usuario->email)
        ->call('enviar');

    Notification::assertSentTo($usuario, ResetPassword::class);
});

test('pedir el enlace para un correo inexistente no revela si la cuenta existe', function () {
    Livewire::test(RecuperarClave::class)
        ->set('correo', 'no-existe@catalina.test')
        ->call('enviar')
        ->assertSet('mensaje', 'Si el correo está registrado, te llega un enlace para restablecer la clave.');
});

test('restablecer la clave con un enlace válido la actualiza e invalida otras sesiones', function () {
    $usuario = User::factory()->cliente()->create(['password' => Hash::make('clave-vieja-123')]);
    $token = Password::createToken($usuario);

    DB::table('sessions')->insert([
        'id' => 'sesion-vieja',
        'user_id' => $usuario->id,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'prueba',
        'payload' => base64_encode('x'),
        'last_activity' => time(),
    ]);

    Livewire::test(RestablecerClave::class, ['token' => $token])
        ->set('correo', $usuario->email)
        ->set('clave', 'clave-nueva-larga-1')
        ->set('clave_confirmation', 'clave-nueva-larga-1')
        ->call('restablecer')
        ->assertRedirect(route('ingresar'));

    expect(Hash::check('clave-nueva-larga-1', $usuario->fresh()->password))->toBeTrue();
    expect(DB::table('sessions')->where('user_id', $usuario->id)->count())->toBe(0);
});

test('un token vencido o inválido no restablece la clave', function () {
    $usuario = User::factory()->cliente()->create();

    Livewire::test(RestablecerClave::class, ['token' => 'token-invalido'])
        ->set('correo', $usuario->email)
        ->set('clave', 'clave-nueva-larga-1')
        ->set('clave_confirmation', 'clave-nueva-larga-1')
        ->call('restablecer')
        ->assertHasErrors('clave');
});
