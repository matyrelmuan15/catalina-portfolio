<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

/**
 * Verifica el token de Cloudflare Turnstile contra el endpoint siteverify
 * (docs/02, sección 8.3). En pruebas, este endpoint se simula con Http::fake().
 */
class Turnstile implements ValidationRule
{
    public function __construct(private readonly ?string $ip = null) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (blank($value)) {
            $fail('Confirmá que no sos un robot.');

            return;
        }

        $respuesta = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => config('services.turnstile.secret_key'),
            'response' => $value,
            'remoteip' => $this->ip,
        ]);

        if (! $respuesta->successful() || $respuesta->json('success') !== true) {
            $fail('No pudimos confirmar que no sos un robot. Probá de nuevo.');
        }
    }
}
