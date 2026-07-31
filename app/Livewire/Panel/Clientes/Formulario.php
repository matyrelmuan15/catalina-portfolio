<?php

namespace App\Livewire\Panel\Clientes;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * RF-21: el alta crea el cliente y su usuario en una sola operación, con
 * clave inicial que la administradora comunica por fuera del sistema. La
 * clave se genera acá mismo (nunca la elige quien completa el formulario)
 * y se muestra una única vez, mientras el modal sigue abierto.
 */
class Formulario extends Component
{
    /**
     * Los mismos seis tonos del prototipo (mockup/portfolio-mockup.html).
     */
    public const TONOS = ['fucsia', 'rosa', 'ciruela', 'arena', 'coral', 'nocturno'];

    #[Validate('required|string|max:120')]
    public string $marca = '';

    #[Validate('required|string|max:120')]
    public string $contacto = '';

    #[Validate('required|email|max:180|unique:users,email')]
    public string $correo = '';

    #[Validate('nullable|string|max:40')]
    public string $telefono = '';

    #[Validate('required|string|in:fucsia,rosa,ciruela,arena,coral,nocturno')]
    public string $color = 'fucsia';

    public ?string $claveGenerada = null;

    public function guardar(): void
    {
        $this->authorize('crear', Cliente::class);
        $this->validate();

        $clave = Str::password(14);

        DB::transaction(function () use ($clave) {
            $cliente = Cliente::create([
                'marca' => $this->marca,
                'contacto' => $this->contacto,
                'correo_contacto' => $this->correo,
                'telefono' => $this->telefono,
                'color' => $this->color,
                'archivado' => false,
            ]);

            User::create([
                'name' => $this->contacto,
                'email' => $this->correo,
                'password' => Hash::make($clave),
                'rol' => 'cliente',
                'cliente_id' => $cliente->id,
                'activo' => true,
                'email_verified_at' => now(),
            ]);
        });

        $this->claveGenerada = $clave;
        $this->dispatch('cliente-creado');
    }

    public function render(): View
    {
        return view('livewire.panel.clientes.formulario');
    }
}
