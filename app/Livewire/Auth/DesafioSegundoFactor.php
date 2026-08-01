<?php

namespace App\Livewire\Auth;

use App\Livewire\Concerns\LimitaIntentos;
use App\Models\User;
use App\Services\AutenticacionDosFactores;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.visitante')]
class DesafioSegundoFactor extends Component
{
    use LimitaIntentos;

    public string $codigo = '';

    public string $error = '';

    public function mount(): void
    {
        if (! session()->has('2fa.pendiente')) {
            $this->redirectRoute('ingresar');
        }
    }

    public function confirmar(AutenticacionDosFactores $totp): void
    {
        $usuarioId = session()->get('2fa.pendiente');
        $clave = $this->limitadorClave('2fa', (string) $usuarioId);

        if ($this->demasiadosIntentos($clave)) {
            $this->error = 'Demasiados intentos. Probá de nuevo en '.$this->segundosDeEspera($clave).' segundos.';

            return;
        }

        $this->validate(['codigo' => ['required', 'digits:6']]);

        $usuario = User::find($usuarioId);

        if (! $usuario || ! $usuario->two_factor_secret || ! $totp->verificar($usuario->two_factor_secret, $this->codigo)) {
            $this->registrarIntento($clave);
            $this->error = 'El código no es válido.';

            return;
        }

        $this->limpiarIntentos($clave);
        session()->forget('2fa.pendiente');

        Auth::login($usuario);
        session()->regenerate();
        $usuario->forceFill(['ultimo_acceso_at' => now()])->save();

        $this->redirectRoute('panel.inicio');
    }

    public function render(): View
    {
        return view('livewire.auth.desafio-segundo-factor');
    }
}
