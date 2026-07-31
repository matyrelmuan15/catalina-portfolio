<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use PragmaRX\Google2FA\Google2FA;

#[Layout('layouts.autenticacion')]
class VerificarSegundoFactor extends Component
{
    public string $codigo = '';

    public function mount(): void
    {
        if (! session('segundo_factor.user_id')) {
            $this->redirectRoute('ingresar', navigate: true);
        }
    }

    public function verificar(): void
    {
        $this->validate(['codigo' => ['required', 'digits:6']], [], ['codigo' => 'código']);

        $usuario = User::find(session('segundo_factor.user_id'));

        if (! $usuario || ! (new Google2FA)->verifyKey((string) $usuario->two_factor_secret, $this->codigo)) {
            $this->addError('codigo', 'El código no es correcto o venció.');

            return;
        }

        $recordarme = (bool) session('segundo_factor.recordarme', false);
        session()->forget(['segundo_factor.user_id', 'segundo_factor.recordarme']);

        Auth::login($usuario, $recordarme);
        session()->regenerate();
        $usuario->forceFill(['ultimo_acceso_at' => now()])->save();

        $this->redirectRoute($usuario->esAdmin() ? 'panel.inicio' : 'portal.inicio', navigate: true);
    }
}
