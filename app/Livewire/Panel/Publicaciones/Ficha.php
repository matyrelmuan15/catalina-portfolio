<?php

namespace App\Livewire\Panel\Publicaciones;

use App\Models\Publicacion;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * RF-41: cada fila del listado abre una ficha con el detalle completo.
 */
#[Layout('layouts.panel')]
class Ficha extends Component
{
    public Publicacion $publicacion;

    public function mount(Publicacion $publicacion): void
    {
        $this->publicacion = $publicacion->load(['cliente', 'metricas']);
    }

    public function render(): View
    {
        return view('livewire.panel.publicaciones.ficha');
    }
}
