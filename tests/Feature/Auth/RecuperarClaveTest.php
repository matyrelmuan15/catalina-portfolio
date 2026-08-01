<?php

use App\Livewire\Auth\RecuperarClave;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

it('envía el enlace de recuperación cuando el correo existe', function () {
    Notification::fake();

    $cliente = Cliente::factory()->create();
    $usuario = User::factory()->for($cliente)->create(['email' => 'julieta@bloomskincare.com.ar']);

    Livewire::test(RecuperarClave::class)
        ->set('correo', 'julieta@bloomskincare.com.ar')
        ->call('enviar')
        ->assertSet('enviado', true);

    Notification::assertSentTo($usuario, ResetPassword::class);
});

it('muestra el mismo mensaje aunque el correo no exista, para no revelar cuentas', function () {
    Notification::fake();

    Livewire::test(RecuperarClave::class)
        ->set('correo', 'no-existe@marca.com')
        ->call('enviar')
        ->assertSet('enviado', true);

    Notification::assertNothingSent();
});
