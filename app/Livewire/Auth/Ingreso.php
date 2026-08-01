<?php

namespace App\Livewire\Auth;

use App\Livewire\Concerns\LimitaIntentos;
use App\Models\User;
use App\Rules\Turnstile;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.visitante')]
class Ingreso extends Component
{
    use LimitaIntentos;

    public string $correo = '';

    public string $clave = '';

    public string $turnstileToken = '';

    public string $error = '';

    public function ingresar(): void
    {
        $clave = $this->limitadorClave('ingreso', mb_strtolower(trim($this->correo)));

        if ($this->demasiadosIntentos($clave)) {
            $this->error = 'Demasiados intentos. Probá de nuevo en '.$this->segundosDeEspera($clave).' segundos.';

            return;
        }

        $this->validate([
            'correo' => ['required', 'email'],
            'clave' => ['required'],
            'turnstileToken' => [new Turnstile(request()->ip())],
        ], [], [
            'correo' => 'correo',
            'clave' => 'clave',
        ]);

        $usuario = User::where('email', mb_strtolower(trim($this->correo)))->first();

        if (! $usuario || ! Hash::check($this->clave, $usuario->password)) {
            $this->registrarIntento($clave);
            $this->error = 'El correo o la clave no coinciden.';

            return;
        }

        if (! $usuario->puedeIniciarSesion()) {
            $this->registrarIntento($clave);
            $this->error = 'Tu acceso está desactivado. Consultá con Catalina.';

            return;
        }

        $this->limpiarIntentos($clave);

        if ($usuario->esAdmin()) {
            session()->regenerate();

            if ($usuario->tieneSegundoFactorActivo()) {
                session(['2fa.pendiente' => $usuario->id]);
                $this->redirectRoute('panel.segundo-factor');
            } else {
                session(['2fa.configurando' => $usuario->id]);
                $this->redirectRoute('panel.segundo-factor.configurar');
            }

            return;
        }

        Auth::login($usuario);
        session()->regenerate();
        $usuario->forceFill(['ultimo_acceso_at' => now()])->save();

        $this->redirectRoute('portal.inicio');
    }

    public function render(): View
    {
        return view('livewire.auth.ingreso');
    }
}
