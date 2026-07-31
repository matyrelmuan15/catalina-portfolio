<?php

use App\Livewire\Auth\VerificarSegundoFactor;
use App\Models\User;
use Livewire\Livewire;
use PragmaRX\Google2FA\Google2FA;

test('un código correcto completa el ingreso del administrador', function () {
    $google2fa = new Google2FA;
    $secreto = $google2fa->generateSecretKey();

    $admin = User::factory()->admin()->create(['two_factor_secret' => $secreto]);

    session(['segundo_factor.user_id' => $admin->id]);

    Livewire::test(VerificarSegundoFactor::class)
        ->set('codigo', $google2fa->getCurrentOtp($secreto))
        ->call('verificar')
        ->assertRedirect(route('panel.inicio'));

    $this->assertAuthenticatedAs($admin);
});

test('un código incorrecto no completa el ingreso', function () {
    $google2fa = new Google2FA;
    $admin = User::factory()->admin()->create(['two_factor_secret' => $google2fa->generateSecretKey()]);

    session(['segundo_factor.user_id' => $admin->id]);

    Livewire::test(VerificarSegundoFactor::class)
        ->set('codigo', '000000')
        ->call('verificar')
        ->assertHasErrors('codigo');

    $this->assertGuest();
});

test('sin un ingreso previo pendiente, la pantalla redirige a ingresar', function () {
    Livewire::test(VerificarSegundoFactor::class)
        ->assertRedirect(route('ingresar'));
});
