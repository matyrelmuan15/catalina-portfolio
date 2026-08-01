<?php

use App\Livewire\Auth\ConfigurarSegundoFactor;
use App\Models\User;
use Livewire\Livewire;
use PragmaRX\Google2FA\Google2FA;

it('manda al ingreso si no hay un enrolamiento pendiente', function () {
    Livewire::test(ConfigurarSegundoFactor::class)
        ->assertRedirect(route('ingresar'));
});

it('guarda el secreto y abre sesión al confirmar el primer código', function () {
    $admin = User::factory()->admin()->create(['two_factor_secret' => null]);
    session(['2fa.configurando' => $admin->id]);

    $prueba = Livewire::test(ConfigurarSegundoFactor::class);
    $secreto = $prueba->get('secreto');

    $codigo = (new Google2FA)->getCurrentOtp($secreto);

    $prueba->set('codigo', $codigo)
        ->call('confirmar')
        ->assertRedirect(route('panel.inicio'));

    expect(auth()->id())->toBe($admin->id);
    expect($admin->fresh()->two_factor_secret)->toBe($secreto);
});

it('rechaza un código de confirmación inválido y no guarda el secreto', function () {
    $admin = User::factory()->admin()->create(['two_factor_secret' => null]);
    session(['2fa.configurando' => $admin->id]);

    Livewire::test(ConfigurarSegundoFactor::class)
        ->set('codigo', '000000')
        ->call('confirmar')
        ->assertSet('error', 'El código no es válido.');

    expect(auth()->check())->toBeFalse();
    expect($admin->fresh()->two_factor_secret)->toBeNull();
});
