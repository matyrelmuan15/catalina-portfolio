<?php

namespace App\Livewire\Panel;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use PragmaRX\Google2FA\Google2FA;

/**
 * RF-93. Mientras la administradora no complete esta pantalla,
 * App\Http\Middleware\AsegurarSegundoFactor la manda siempre acá.
 */
#[Layout('layouts.panel')]
class ConfigurarSegundoFactor extends Component
{
    public string $secreto = '';

    public string $codigo = '';

    public function mount(): void
    {
        $usuario = Auth::user();

        if ($usuario->tieneSegundoFactorActivo()) {
            $this->secreto = '';

            return;
        }

        $this->secreto = session('dos_factores.secreto_temporal') ?: (new Google2FA)->generateSecretKey();
        session(['dos_factores.secreto_temporal' => $this->secreto]);
    }

    public function urlQr(): string
    {
        $google2fa = new Google2FA;

        return $google2fa->getQRCodeUrl(
            config('app.name'),
            Auth::user()->email,
            $this->secreto,
        );
    }

    public function activar(): void
    {
        $this->validate(['codigo' => ['required', 'digits:6']], [], ['codigo' => 'código']);

        $google2fa = new Google2FA;

        if (! $google2fa->verifyKey($this->secreto, $this->codigo)) {
            $this->addError('codigo', 'El código no coincide. Revisá la hora del teléfono e intentá de nuevo.');

            return;
        }

        Auth::user()->forceFill(['two_factor_secret' => $this->secreto])->save();
        session()->forget('dos_factores.secreto_temporal');

        session()->flash('estado', 'Segundo factor activado.');
        $this->redirectRoute('panel.inicio', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.panel.configurar-segundo-factor');
    }
}
