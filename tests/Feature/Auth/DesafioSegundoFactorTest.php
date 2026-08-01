<?php

use App\Livewire\Auth\DesafioSegundoFactor;
use App\Models\User;
use Livewire\Livewire;
use PragmaRX\Google2FA\Google2FA;

it('manda al ingreso si no hay un desafío pendiente', function () {
    Livewire::test(DesafioSegundoFactor::class)
        ->assertRedirect(route('ingresar'));
});

it('abre sesión con un código válido', function () {
    $secreto = (new Google2FA)->generateSecretKey();
    $admin = User::factory()->admin()->create(['two_factor_secret' => $secreto]);

    session(['2fa.pendiente' => $admin->id]);

    $codigo = (new Google2FA)->getCurrentOtp($secreto);

    Livewire::test(DesafioSegundoFactor::class)
        ->set('codigo', $codigo)
        ->call('confirmar')
        ->assertRedirect(route('panel.inicio'));

    expect(auth()->id())->toBe($admin->id);
    expect(session('2fa.pendiente'))->toBeNull();
});

it('rechaza un código inválido', function () {
    $secreto = (new Google2FA)->generateSecretKey();
    $admin = User::factory()->admin()->create(['two_factor_secret' => $secreto]);

    session(['2fa.pendiente' => $admin->id]);

    Livewire::test(DesafioSegundoFactor::class)
        ->set('codigo', '000000')
        ->call('confirmar')
        ->assertSet('error', 'El código no es válido.');

    expect(auth()->check())->toBeFalse();
});
