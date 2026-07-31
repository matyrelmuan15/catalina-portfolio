<?php

namespace App\Livewire\Portal;

use App\Concerns\ConstruyeCalendarioMensual;
use App\Models\AgendaEvento;
use App\Models\Publicacion;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * RF-30 a RF-34, del lado del cliente: solo consulta. El cliente activo
 * sale siempre de la sesión (Auth::user()->cliente_id) — nunca de un
 * parámetro de la petición, que es justamente el nivel 3 del aislamiento
 * (docs/02-arquitectura-y-datos.md §6.1).
 */
#[Layout('layouts.portal')]
class Calendario extends Component
{
    use ConstruyeCalendarioMensual;

    public string $mes;

    public function mount(): void
    {
        $this->mes = today()->startOfMonth()->toDateString();
    }

    public function mesAnterior(): void
    {
        $this->mes = Carbon::parse($this->mes)->subMonthNoOverflow()->toDateString();
    }

    public function mesSiguiente(): void
    {
        $this->mes = Carbon::parse($this->mes)->addMonthNoOverflow()->toDateString();
    }

    public function render(): View
    {
        $mes = Carbon::parse($this->mes);
        $rango = [
            $mes->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY)->toDateString(),
            $mes->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY)->toDateString(),
        ];

        // El global scope PerteneceAlCliente ya filtra estas consultas por el
        // cliente_id de la sesión: no hace falta (ni se debe) agregar un where acá.
        $eventosDelMes = AgendaEvento::query()->whereBetween('fecha', $rango)->get()
            ->concat(Publicacion::query()->whereBetween('fecha', $rango)->get());

        return view('livewire.portal.calendario', [
            'mesActual' => $mes,
            'dias' => $this->diasDelMes($mes, $eventosDelMes),
            'proximas' => AgendaEvento::query()->proximos()->limit(5)->get(),
            'marca' => Auth::user()->cliente->marca,
        ]);
    }
}
