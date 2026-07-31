<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as ReglaClave;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.autenticacion')]
class RestablecerClave extends Component
{
    public string $token = '';

    public string $correo = '';

    public string $clave = '';

    public string $clave_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->correo = request()->query('correo', '');
    }

    public function restablecer(): void
    {
        $this->validate([
            'correo' => ['required', 'email'],
            'clave' => ['required', 'confirmed', ReglaClave::min(10)->uncompromised()],
        ], [], ['correo' => 'correo', 'clave' => 'clave']);

        $estado = Password::reset(
            [
                'email' => $this->correo,
                'password' => $this->clave,
                'password_confirmation' => $this->clave_confirmation,
                'token' => $this->token,
            ],
            function (User $usuario) {
                $usuario->forceFill(['password' => Hash::make($this->clave)]);
                $usuario->setRememberToken(Str::random(60));
                $usuario->save();

                // Invalida el resto de las sesiones abiertas de este usuario.
                DB::table('sessions')->where('user_id', $usuario->id)->delete();

                event(new PasswordReset($usuario));
            }
        );

        if ($estado !== Password::PASSWORD_RESET) {
            $this->addError('clave', 'El enlace venció o ya se usó. Pedí uno nuevo.');

            return;
        }

        session()->flash('estado', 'Tu clave se actualizó. Ya podés ingresar.');
        $this->redirectRoute('ingresar', navigate: true);
    }
}
