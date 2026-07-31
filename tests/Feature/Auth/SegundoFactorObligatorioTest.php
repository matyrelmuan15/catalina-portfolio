<?php

use App\Models\User;

test('un administrador sin segundo factor activo siempre cae en la pantalla de configurarlo', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get('/panel/videos')
        ->assertRedirect(route('panel.cuenta.dos-factores'));
});

test('un administrador con segundo factor activo navega el panel sin trabas', function () {
    $admin = User::factory()->admin()->create(['two_factor_secret' => 'secreto-de-prueba']);

    $this->actingAs($admin)
        ->get('/panel/videos')
        ->assertOk();
});
