<?php

namespace App\Livewire\Publico;

use App\Models\Video;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Portfolio público (docs/01-especificacion-funcional.md §3).
 *
 * El filtro por categoría se resuelve en el cliente con Alpine (que Livewire
 * ya trae incluido): todas las piezas publicadas se renderizan una sola vez
 * y el filtro solo cambia qué se ve, sin ida y vuelta al servidor. Así se
 * cumple RF-03 (no recarga la página) y el margen de 100 ms de la fase 2
 * sin depender de la latencia de red.
 */
#[Layout('layouts.publico')]
class Portada extends Component
{
    /**
     * @return Collection<int, Video>
     */
    public function videos(): Collection
    {
        return Video::query()->publicados()->ordenDelPortfolio()->get();
    }

    public function render(): View
    {
        return view('livewire.publico.portada', [
            'videos' => $this->videos(),
            'categorias' => Video::CATEGORIAS,
        ]);
    }
}
