<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as ReglaClave;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.visitante')]
class RestablecerClave extends Component
{
    public string $token = '';

    public string $correo = '';

    public string $clave = '';

    // El nombre exacto importa: la regla "confirmed" de Laravel busca
    // "{campo}_confirmation", acá "clave_confirmation".
    public string $clave_confirmation = '';

    public string $error = '';

    public bool $listo = false;

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->correo = mb_strtolower(trim((string) request()->query('correo', '')));
    }

    public function restablecer(): void
    {
        $this->validate([
            'correo' => ['required', 'email'],
            'clave' => ['required', 'confirmed', ReglaClave::min(10)->uncompromised()],
        ], [], [
            'clave' => 'clave',
        ]);

        $estado = Password::reset(
            [
                'email' => $this->correo,
                'password' => $this->clave,
                'password_confirmation' => $this->clave_confirmation,
                'token' => $this->token,
            ],
            function (User $usuario) {
                $usuario->forceFill(['password' => Hash::make($this->clave)])->save();

                // Invalidación de todas las sesiones al cambiar la clave
                // (docs/02, sección 6.2). No había sesión propia abierta acá:
                // el reintento se hace siempre desde el ingreso.
                DB::table('sessions')->where('user_id', $usuario->id)->delete();

                Event::dispatch(new PasswordReset($usuario));
            }
        );

        if ($estado !== Password::PASSWORD_RESET) {
            $this->error = 'El enlace no es válido o ya venció. Pedí uno nuevo.';

            return;
        }

        $this->listo = true;
    }

    public function render(): View
    {
        return view('livewire.auth.restablecer-clave');
    }
}
