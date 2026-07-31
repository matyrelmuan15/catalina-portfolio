<?php

namespace App\Livewire\Publico;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Portfolio público. La estructura completa (RF-01 a RF-08) se construye
 * en la fase 2; por ahora solo confirma que el sitio responde y que el
 * enlace de ingreso funciona (requisito de la fase 0 y la fase 1).
 */
#[Layout('layouts.publico')]
class Portada extends Component
{
    public function render(): View
    {
        return view('livewire.publico.portada');
    }
}
