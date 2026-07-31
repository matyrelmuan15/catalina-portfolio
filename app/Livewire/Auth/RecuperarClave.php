<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * RF-92: recuperación de clave por enlace con vencimiento de 60 minutos
 * (el vencimiento lo controla config/auth.php, valor por defecto de Laravel).
 */
#[Layout('layouts.autenticacion')]
class RecuperarClave extends Component
{
    public string $correo = '';

    public ?string $mensaje = null;

    public function enviar(): void
    {
        $this->validate(['correo' => ['required', 'email']], [], ['correo' => 'correo']);

        Password::sendResetLink(['email' => $this->correo]);

        // Se responde igual exista o no la cuenta: no se confirma qué correos están dados de alta.
        $this->mensaje = 'Si el correo está registrado, te llega un enlace para restablecer la clave.';
        $this->correo = '';
    }
}
