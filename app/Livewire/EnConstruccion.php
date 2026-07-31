<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * Placeholder de pantallas que corresponden a fases posteriores del plan
 * (docs/04-plan-de-fases.md). Solo existe para que la navegación del
 * armazón funcione de punta a punta desde la fase 1.
 */
class EnConstruccion extends Component
{
    public string $titulo;

    public string $descripcion;

    public function mount(string $titulo, string $descripcion = 'Esta pantalla se construye en una fase posterior.'): void
    {
        $this->titulo = $titulo;
        $this->descripcion = $descripcion;
    }

    public function render(): View
    {
        $layout = request()->routeIs('portal.*') ? 'layouts.portal' : 'layouts.panel';

        return view('livewire.en-construccion')->layout($layout, ['title' => $this->titulo]);
    }
}
