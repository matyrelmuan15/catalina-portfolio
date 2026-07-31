<?php

namespace App\Livewire\Panel\Publicaciones;

use App\Models\Cliente;
use App\Models\Publicacion;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * RF-40: tabla con las 25 columnas, desplazamiento horizontal y primera
 * columna fija. Se usa tanto en /panel/publicaciones (todas las marcas)
 * como en la solapa "Publicaciones" de la ficha de un cliente, pasándole
 * $cliente para acotarla.
 *
 * Se guarda solo el id del cliente, no el modelo completo: una propiedad
 * pública tipada como modelo Eloquent que puede ser null, sin valor
 * explícito, se rehidrata en Livewire como una instancia vacía (no
 * persistida) en vez de null, y esa instancia es un objeto — siempre
 * "truthy" en PHP —, lo que rompía el filtro por cliente en el listado
 * global (terminaba comparando cliente_id contra null).
 */
#[Layout('layouts.panel')]
class Listado extends Component
{
    public ?int $clienteId = null;

    public string $busqueda = '';

    public string $estadoFiltro = '';

    public bool $modalAbierto = false;

    public ?int $editandoId = null;

    public function mount(?Cliente $cliente = null): void
    {
        $this->clienteId = $cliente?->id;
    }

    public function abrirAlta(): void
    {
        $this->editandoId = null;
        $this->modalAbierto = true;
    }

    public function abrirEdicion(int $id): void
    {
        $this->editandoId = $id;
        $this->modalAbierto = true;
    }

    #[On('publicacion-guardada')]
    public function cerrarModal(): void
    {
        $this->modalAbierto = false;
        $this->editandoId = null;
    }

    public function eliminar(int $id): void
    {
        Publicacion::findOrFail($id)->delete();
    }

    /**
     * @return Collection<int, Publicacion>
     */
    public function publicacionesFiltradas(): Collection
    {
        return Publicacion::query()
            ->with(['cliente', 'ultimaMetrica'])
            ->when($this->clienteId, fn ($query) => $query->where('cliente_id', $this->clienteId))
            ->when($this->busqueda, fn ($query) => $query->where('titulo', 'like', "%{$this->busqueda}%"))
            ->when($this->estadoFiltro, fn ($query) => $query->where('estado', $this->estadoFiltro))
            ->orderByDesc('fecha')
            ->get();
    }

    public function render(): View
    {
        return view('livewire.panel.publicaciones.listado', [
            'publicaciones' => $this->publicacionesFiltradas(),
            'estados' => Publicacion::ESTADOS,
            'cliente' => $this->clienteId ? Cliente::find($this->clienteId) : null,
        ]);
    }
}
