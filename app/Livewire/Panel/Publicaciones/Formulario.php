<?php

namespace App\Livewire\Panel\Publicaciones;

use App\Models\Cliente;
use App\Models\Publicacion;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * RF-42: alta y edición de los campos propios de una publicación (grupo C),
 * más lo esencial para planificarla antes de que exista el import de la
 * fase 7 (fecha, plataforma, formato).
 */
class Formulario extends Component
{
    public ?int $publicacionId = null;

    /**
     * Si viene seteado, el select de cliente no se muestra: la publicación
     * se crea directo para esa marca (se abre desde su ficha).
     */
    public ?int $clienteFijoId = null;

    #[Validate('required|exists:clientes,id')]
    public ?int $cliente_id = null;

    #[Validate('required|string|max:160')]
    public string $titulo = '';

    #[Validate('required|string|in:Planificada,En producción,Aprobada,Publicada')]
    public string $estado = 'Planificada';

    #[Validate('nullable|string|max:40')]
    public string $pilar = '';

    #[Validate('required|date')]
    public string $fecha = '';

    #[Validate('required|string|in:instagram,facebook')]
    public string $plataforma = 'instagram';

    #[Validate('required|string|in:reel,carrusel,imagen,historia,video')]
    public string $formato = 'reel';

    #[Validate('nullable|url|max:300')]
    public string $archivo_final_url = '';

    #[Validate('nullable|url|max:300')]
    public string $creativo_figma_url = '';

    public function mount(?int $publicacionId = null, ?Cliente $cliente = null): void
    {
        $this->publicacionId = $publicacionId;
        $this->clienteFijoId = $cliente?->id;
        $this->cliente_id = $cliente?->id;

        if ($publicacionId) {
            $publicacion = Publicacion::findOrFail($publicacionId);
            $this->cliente_id = $publicacion->cliente_id;
            $this->titulo = $publicacion->titulo;
            $this->estado = $publicacion->estado === 'Medida' ? 'Publicada' : $publicacion->estado;
            $this->pilar = (string) $publicacion->pilar;
            $this->fecha = Carbon::parse($publicacion->fecha)->toDateString();
            $this->plataforma = $publicacion->plataforma;
            $this->formato = $publicacion->formato;
            $this->archivo_final_url = (string) $publicacion->archivo_final_url;
            $this->creativo_figma_url = (string) $publicacion->creativo_figma_url;
        }
    }

    public function guardar(): void
    {
        $this->validate();

        $publicacion = $this->publicacionId ? Publicacion::findOrFail($this->publicacionId) : new Publicacion;

        // "Medida" solo la pone el import de la fase 7: si ya estaba medida,
        // editar acá no la hace retroceder de estado.
        $estado = $publicacion->exists && $publicacion->estado === 'Medida' ? 'Medida' : $this->estado;

        $publicacion->fill([
            'cliente_id' => $this->cliente_id,
            'titulo' => $this->titulo,
            'estado' => $estado,
            'pilar' => $this->pilar ?: null,
            'fecha' => $this->fecha,
            'plataforma' => $this->plataforma,
            'formato' => $this->formato,
            'archivo_final_url' => $this->archivo_final_url ?: null,
            'creativo_figma_url' => $this->creativo_figma_url ?: null,
        ])->save();

        $this->dispatch('publicacion-guardada');
    }

    public function render(): View
    {
        return view('livewire.panel.publicaciones.formulario', [
            'clientes' => $this->clienteFijoId ? collect() : Cliente::query()->activos()->orderBy('marca')->get(),
        ]);
    }
}
