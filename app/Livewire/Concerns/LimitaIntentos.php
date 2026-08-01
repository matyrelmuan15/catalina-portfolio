<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/**
 * Límite de cinco intentos por minuto por IP y por identificador (RF-91,
 * docs/02 sección 6.2). Se reutiliza en el ingreso y en el desafío del
 * segundo factor.
 */
trait LimitaIntentos
{
    protected function limitadorClave(string $prefijo, string $identificador): string
    {
        return Str::lower($prefijo.'|'.$identificador.'|'.request()->ip());
    }

    protected function demasiadosIntentos(string $clave, int $maximo = 5): bool
    {
        return RateLimiter::tooManyAttempts($clave, $maximo);
    }

    protected function registrarIntento(string $clave, int $segundos = 60): void
    {
        RateLimiter::hit($clave, $segundos);
    }

    protected function limpiarIntentos(string $clave): void
    {
        RateLimiter::clear($clave);
    }

    protected function segundosDeEspera(string $clave): int
    {
        return RateLimiter::availableIn($clave);
    }
}
