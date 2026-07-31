<?php

namespace App\Livewire\Portal;

use App\Models\Publicacion;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * RF-44: el cliente ve un subconjunto de columnas, sin estado, ID Media,
 * creativo, copy, hashtags, clics, seguidores ni datos internos. El global
 * scope PerteneceAlCliente ya limita esta consulta a la marca de la sesión.
 */
#[Layout('layouts.portal')]
class Publicaciones extends Component
{
    /**
     * @return Collection<int, Publicacion>
     */
    public function publicaciones(): Collection
    {
        return Publicacion::query()->with('ultimaMetrica')->orderByDesc('fecha')->get();
    }

    public function render(): View
    {
        return view('livewire.portal.publicaciones', [
            'publicaciones' => $this->publicaciones(),
        ]);
    }
}
