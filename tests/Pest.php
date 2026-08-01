<?php

/*
|--------------------------------------------------------------------------
| Caso de prueba
|--------------------------------------------------------------------------
|
| RefreshDatabase corre las migraciones sobre la base de pruebas antes de
| cada test. La conexión la define phpunit.xml (PostgreSQL, nunca SQLite).
|
*/

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(function () {
        // Turnstile aprueba por defecto. Http::fake() no reemplaza reglas
        // anteriores para el mismo patrón de URL —la primera que matchea
        // gana—, así que un rechazo se simula mandando el token especial
        // 'token-rechazado' en vez de volver a llamar a Http::fake().
        Http::fake([
            'challenges.cloudflare.com/*' => fn ($request) => Http::response([
                'success' => $request['response'] !== 'token-rechazado',
            ]),
        ]);
    })
    ->in('Feature', 'Unit');
