<?php

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * El scope PerteneceAlCliente se aplica al modelo User, que es el único que el
 * propio guard de sesión consulta para resolver quién está autenticado. Estas
 * pruebas cubren ese cruce: que resolver la sesión no entre en recursión y que,
 * una vez resuelta, el aislamiento entre clientes siga valiendo sobre `users`.
 *
 * No usan actingAs a propósito: actingAs asigna el usuario directamente al
 * guard y nunca dispara EloquentUserProvider::retrieveById, que es la consulta
 * donde estaba la recursión.
 */
it('resuelve al usuario desde la sesión sin recursión infinita', function () {
    $cliente = Cliente::factory()->create();
    $usuario = User::factory()->for($cliente)->create();

    $this->withSession([Auth::getName() => $usuario->id])
        ->get('/portal/calendario')
        ->assertOk();

    expect(auth()->id())->toBe($usuario->id);
});

it('resuelve a la administradora desde la sesión sin recursión infinita', function () {
    $admin = User::factory()->admin()->create(['two_factor_secret' => 'SECRETODEPRUEBA']);

    $this->withSession([Auth::getName() => $admin->id])
        ->get('/panel/videos')
        ->assertOk();

    expect(auth()->id())->toBe($admin->id);
});

it('un cliente autenticado no ve las cuentas de otra marca', function () {
    $propio = Cliente::factory()->create();
    $ajeno = Cliente::factory()->create();

    $usuario = User::factory()->for($propio)->create();
    $companiero = User::factory()->for($propio)->create();
    $extranio = User::factory()->for($ajeno)->create();
    $admin = User::factory()->admin()->create();

    $this->actingAs($usuario);

    $visibles = User::pluck('id');

    expect($visibles)->toContain($usuario->id)
        ->toContain($companiero->id)
        ->not->toContain($extranio->id)
        ->not->toContain($admin->id);

    expect(User::find($extranio->id))->toBeNull();
});

it('la administradora sigue viendo todas las cuentas', function () {
    $admin = User::factory()->admin()->create();
    $uno = User::factory()->for(Cliente::factory())->create();
    $otro = User::factory()->for(Cliente::factory())->create();

    $this->actingAs($admin);

    expect(User::pluck('id'))
        ->toContain($uno->id)
        ->toContain($otro->id)
        ->toContain($admin->id);
});

it('el ingreso puede encontrar cuentas de cualquier marca porque todavía no hay sesión', function () {
    $usuario = User::factory()->for(Cliente::factory())->create([
        'email' => 'julieta@bloomskincare.com.ar',
    ]);

    expect(User::where('email', 'julieta@bloomskincare.com.ar')->first()?->id)
        ->toBe($usuario->id);
});
