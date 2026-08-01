<?php

namespace App\Livewire\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.visitante')]
class RecuperarClave extends Component
{
    public string $correo = '';

    public bool $enviado = false;

    public function enviar(): void
    {
        $this->validate(['correo' => ['required', 'email']]);

        Password::sendResetLink(['email' => mb_strtolower(trim($this->correo))]);

        // El mensaje es el mismo exista o no la cuenta: no hay que revelar
        // qué correos están registrados.
        $this->enviado = true;
    }

    public function render(): View
    {
        return view('livewire.auth.recuperar-clave');
    }
}
