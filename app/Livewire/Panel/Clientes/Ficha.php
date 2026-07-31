<?php

namespace App\Livewire\Panel\Clientes;

use App\Models\Cliente;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * RF-24: ficha con cuatro solapas (calendario, publicaciones, pedidos,
 * métricas). Todavía vacías: se completan en las fases 5, 6, 9 y 8.
 */
#[Layout('layouts.panel')]
class Ficha extends Component
{
    public Cliente $cliente;

    public string $solapa = 'calendario';

    public ?string $claveGenerada = null;

    public function mount(Cliente $cliente): void
    {
        $this->authorize('ver', $cliente);
        $this->cliente = $cliente;
    }

    public function cambiarSolapa(string $solapa): void
    {
        $this->solapa = $solapa;
    }

    public function archivar(): void
    {
        $this->authorize('archivar', $this->cliente);
        $this->cliente->update(['archivado' => true]);
    }

    public function desarchivar(): void
    {
        $this->authorize('archivar', $this->cliente);
        $this->cliente->update(['archivado' => false]);
    }

    /**
     * RF-23: la administradora puede restablecer la clave de un cliente
     * desde la ficha. Se genera una clave nueva al azar y se muestra una
     * sola vez; la comunicación al cliente queda por fuera del sistema.
     */
    public function restablecerClave(): void
    {
        $this->authorize('restablecerClave', $this->cliente);

        $usuario = $this->cliente->users()->first();
        abort_if($usuario === null, 404);

        $clave = Str::password(14);
        $usuario->forceFill(['password' => Hash::make($clave)])->save();

        $this->claveGenerada = $clave;
    }

    public function render(): View
    {
        return view('livewire.panel.clientes.ficha');
    }
}
