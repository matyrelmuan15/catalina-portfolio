<?php

namespace App\Livewire\Panel\Clientes;

use App\Concerns\ConstruyeCalendarioMensual;
use App\Models\Cliente;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * RF-30 a RF-34, del lado del panel: alta, edición y baja de eventos de un
 * cliente. Solo la administradora llega hasta acá (vive dentro de
 * App\Livewire\Panel\Clientes\Ficha, detrás de rol.admin).
 */
class Calendario extends Component
{
    use ConstruyeCalendarioMensual;

    public Cliente $cliente;

    public string $mes;

    public bool $modalAbierto = false;

    public ?int $editandoId = null;

    #[Validate('required|date')]
    public string $fecha = '';

    #[Validate('required|string|in:grabacion,entrega,reunion')]
    public string $tipo = 'grabacion';

    #[Validate('required|string|max:160')]
    public string $titulo = '';

    #[Validate('nullable|string|max:1000')]
    public string $nota = '';

    public function mount(Cliente $cliente): void
    {
        $this->cliente = $cliente;
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

    public function abrirAlta(?string $fecha = null): void
    {
        $this->resetearFormulario();
        $this->fecha = $fecha ?? today()->toDateString();
        $this->modalAbierto = true;
    }

    public function abrirEdicion(int $id): void
    {
        $evento = $this->cliente->agendaEventos()->findOrFail($id);

        $this->editandoId = $evento->id;
        $this->fecha = Carbon::parse($evento->fecha)->toDateString();
        $this->tipo = $evento->tipo;
        $this->titulo = $evento->titulo;
        $this->nota = (string) $evento->nota;
        $this->modalAbierto = true;
    }

    public function cerrarModal(): void
    {
        $this->modalAbierto = false;
        $this->resetearFormulario();
    }

    public function guardar(): void
    {
        $this->validate();

        $this->cliente->agendaEventos()->updateOrCreate(
            ['id' => $this->editandoId],
            [
                'fecha' => $this->fecha,
                'tipo' => $this->tipo,
                'titulo' => $this->titulo,
                'nota' => $this->nota,
            ]
        );

        $this->cerrarModal();
    }

    public function eliminar(int $id): void
    {
        $this->cliente->agendaEventos()->findOrFail($id)->delete();
    }

    private function resetearFormulario(): void
    {
        $this->editandoId = null;
        $this->fecha = '';
        $this->tipo = 'grabacion';
        $this->titulo = '';
        $this->nota = '';
        $this->resetErrorBag();
    }

    public function render(): View
    {
        $mes = Carbon::parse($this->mes);
        $rango = [
            $mes->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY)->toDateString(),
            $mes->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY)->toDateString(),
        ];

        // RF-43: las publicaciones aparecen en el calendario en su fecha,
        // aunque no son eventos de agenda (docs/01-especificacion-funcional.md §6.1).
        $eventosDelMes = $this->cliente->agendaEventos()->whereBetween('fecha', $rango)->get()
            ->concat($this->cliente->publicaciones()->whereBetween('fecha', $rango)->get());

        return view('livewire.panel.clientes.calendario', [
            'mesActual' => $mes,
            'dias' => $this->diasDelMes($mes, $eventosDelMes),
            'proximas' => $this->cliente->agendaEventos()->proximos()->limit(5)->get(),
        ]);
    }
}
