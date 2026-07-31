<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Ingreso con correo y clave (RF-90). Límite de cinco intentos por minuto
 * por IP (RF-91) y, para la administradora, exige el segundo factor antes
 * de abrir sesión de verdad (RF-93).
 */
#[Layout('layouts.autenticacion')]
class Ingreso extends Component
{
    public string $correo = '';

    public string $clave = '';

    public bool $recordarme = false;

    public function ingresar(): void
    {
        $this->validate([
            'correo' => ['required', 'email'],
            'clave' => ['required', 'string'],
        ], [], [
            'correo' => 'correo',
            'clave' => 'clave',
        ]);

        $claveLimite = 'ingreso:'.request()->ip();

        if (RateLimiter::tooManyAttempts($claveLimite, 5)) {
            $segundos = RateLimiter::availableIn($claveLimite);

            $this->addError('ingreso', "Demasiados intentos. Probá de nuevo en {$segundos} segundos.");

            return;
        }

        $usuario = User::where('email', $this->correo)->first();

        if (! $usuario
            || ! $usuario->activo
            || ($usuario->esCliente() && ($usuario->cliente === null || $usuario->cliente->archivado))
            || ! Auth::validate(['email' => $this->correo, 'password' => $this->clave])
        ) {
            RateLimiter::hit($claveLimite, 60);
            $this->addError('ingreso', 'El correo o la clave no son correctos.');

            return;
        }

        RateLimiter::clear($claveLimite);

        if ($usuario->tieneSegundoFactorActivo()) {
            session(['segundo_factor.user_id' => $usuario->id, 'segundo_factor.recordarme' => $this->recordarme]);
            $this->redirectRoute('ingresar.verificar', navigate: true);

            return;
        }

        Auth::login($usuario, $this->recordarme);
        session()->regenerate();
        $usuario->forceFill(['ultimo_acceso_at' => now()])->save();

        $this->redirectRoute($usuario->esAdmin() ? 'panel.inicio' : 'portal.inicio', navigate: true);
    }
}
