<?php

namespace App\Livewire\Auth;

use App\Livewire\Concerns\LimitaIntentos;
use App\Models\User;
use App\Services\AutenticacionDosFactores;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Enrolamiento obligatorio del segundo factor la primera vez que la
 * administradora inicia sesión (RF-93). El secreto recién se guarda en la
 * base cuando confirma un código válido; hasta entonces vive solo en sesión.
 */
#[Layout('layouts.visitante')]
class ConfigurarSegundoFactor extends Component
{
    use LimitaIntentos;

    public string $codigo = '';

    public string $error = '';

    public string $secreto = '';

    public string $qrSvg = '';

    public function mount(AutenticacionDosFactores $totp): void
    {
        $usuarioId = session()->get('2fa.configurando');

        if (! $usuarioId) {
            $this->redirectRoute('ingresar');

            return;
        }

        $usuario = User::find($usuarioId);

        $this->secreto = session()->get('2fa.secreto_temporal') ?: $totp->generarSecreto();
        session(['2fa.secreto_temporal' => $this->secreto]);

        $this->qrSvg = $totp->codigoQrSvg($usuario->email, $this->secreto);
    }

    public function confirmar(AutenticacionDosFactores $totp): void
    {
        $usuarioId = session()->get('2fa.configurando');
        $clave = $this->limitadorClave('2fa-setup', (string) $usuarioId);

        if ($this->demasiadosIntentos($clave)) {
            $this->error = 'Demasiados intentos. Probá de nuevo en '.$this->segundosDeEspera($clave).' segundos.';

            return;
        }

        $this->validate(['codigo' => ['required', 'digits:6']]);

        if (! $totp->verificar($this->secreto, $this->codigo)) {
            $this->registrarIntento($clave);
            $this->error = 'El código no es válido.';

            return;
        }

        $this->limpiarIntentos($clave);

        $usuario = User::findOrFail($usuarioId);
        $usuario->forceFill([
            'two_factor_secret' => $this->secreto,
            'ultimo_acceso_at' => now(),
        ])->save();

        session()->forget(['2fa.configurando', '2fa.secreto_temporal']);

        Auth::login($usuario);
        session()->regenerate();

        $this->redirectRoute('panel.inicio');
    }

    public function render(): View
    {
        return view('livewire.auth.configurar-segundo-factor');
    }
}
