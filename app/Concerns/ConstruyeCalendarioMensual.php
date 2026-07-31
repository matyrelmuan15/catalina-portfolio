<?php

namespace App\Concerns;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Arma la grilla de seis semanas (lunes a domingo) de un mes, con los
 * eventos de cada día ya agrupados. La usan tanto el calendario del panel
 * (App\Livewire\Panel\Clientes\Calendario) como el del portal
 * (App\Livewire\Portal\Calendario) para no duplicar la lógica de fechas.
 */
trait ConstruyeCalendarioMensual
{
    /**
     * @param  Collection<int, mixed>  $eventos
     * @return Collection<int, array{fecha: Carbon, otroMes: bool, hoy: bool, eventos: Collection<int, mixed>}>
     */
    protected function diasDelMes(Carbon $mes, Collection $eventos): Collection
    {
        $eventosPorDia = $eventos->groupBy(fn (mixed $evento): string => $evento->fecha->toDateString());

        $inicioGrilla = $mes->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $hoy = today();

        return collect(range(0, 41))->map(function (int $offset) use ($inicioGrilla, $mes, $hoy, $eventosPorDia) {
            $fecha = $inicioGrilla->copy()->addDays($offset);

            return [
                'fecha' => $fecha,
                'otroMes' => ! $fecha->isSameMonth($mes),
                'hoy' => $fecha->isSameDay($hoy),
                'eventos' => $eventosPorDia->get($fecha->toDateString(), collect()),
            ];
        });
    }
}
