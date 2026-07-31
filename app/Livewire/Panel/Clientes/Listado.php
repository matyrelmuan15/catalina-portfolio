<?php

namespace App\Livewire\Panel\Clientes;

use App\Models\Cliente;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * RF-20: listado con marca, contacto, próxima fecha comprometida y
 * cantidad de pedidos abiertos. Las dos últimas columnas dependen de
 * "agenda_eventos" (fase 5) y "pedidos" (fase 9): hasta que existan esas
 * tablas se muestran como guion, nunca como cero (mismo criterio que
 * docs/01-especificacion-funcional.md RF-65 para no leer un vacío como dato).
 */
#[Layout('layouts.panel')]
class Listado extends Component
{
    public string $busqueda = '';

    public bool $verArchivados = false;

    public bool $modalAbierto = false;

    public function cerrarModal(): void
    {
        $this->modalAbierto = false;
    }

    public function abrirAlta(): void
    {
        $this->modalAbierto = true;
    }

    public function archivar(int $id): void
    {
        $cliente = Cliente::findOrFail($id);
        $this->authorize('archivar', $cliente);

        $cliente->update(['archivado' => true]);
    }

    public function desarchivar(int $id): void
    {
        $cliente = Cliente::findOrFail($id);
        $this->authorize('archivar', $cliente);

        $cliente->update(['archivado' => false]);
    }

    /**
     * @return Collection<int, Cliente>
     */
    public function clientesFiltrados(): Collection
    {
        return Cliente::query()
            ->when(! $this->verArchivados, fn ($query) => $query->where('archivado', false))
            ->when($this->busqueda, fn ($query) => $query->where(function ($sub) {
                $sub->where('marca', 'like', "%{$this->busqueda}%")
                    ->orWhere('contacto', 'like', "%{$this->busqueda}%");
            }))
            ->orderBy('marca')
            ->get();
    }

    public function render(): View
    {
        return view('livewire.panel.clientes.listado', [
            'clientes' => $this->clientesFiltrados(),
        ]);
    }
}
