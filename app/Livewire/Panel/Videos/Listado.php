<?php

namespace App\Livewire\Panel\Videos;

use App\Models\Video;
use App\Services\ProcesadorMiniatura;
use App\Services\PurgadorCacheCloudflare;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * RF-10 a RF-14: listado con buscador, filtro, contadores y alternado
 * rápido de publicado/destacado sin abrir el formulario.
 */
#[Layout('layouts.panel')]
class Listado extends Component
{
    public string $busqueda = '';

    public string $categoriaFiltro = '';

    public bool $modalAbierto = false;

    public ?int $editandoId = null;

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

    #[On('video-guardado')]
    public function cerrarModal(): void
    {
        $this->modalAbierto = false;
        $this->editandoId = null;
    }

    public function alternarPublicado(int $id): void
    {
        $video = Video::findOrFail($id);
        $video->update(['publicado' => ! $video->publicado]);

        app(PurgadorCacheCloudflare::class)->purgarPortada();
    }

    public function alternarDestacado(int $id): void
    {
        $video = Video::findOrFail($id);
        $video->update(['destacado' => ! $video->destacado]);

        app(PurgadorCacheCloudflare::class)->purgarPortada();
    }

    public function eliminar(int $id): void
    {
        $video = Video::findOrFail($id);
        app(ProcesadorMiniatura::class)->eliminar($video->miniatura_path);
        $video->delete();

        app(PurgadorCacheCloudflare::class)->purgarPortada();
    }

    /**
     * @return Collection<int, Video>
     */
    public function videosFiltrados(): Collection
    {
        return Video::query()
            ->when($this->busqueda, fn ($query) => $query->where(function ($sub) {
                $sub->where('titulo', 'like', "%{$this->busqueda}%")
                    ->orWhere('cliente_texto', 'like', "%{$this->busqueda}%");
            }))
            ->when($this->categoriaFiltro, fn ($query) => $query->where('categoria', $this->categoriaFiltro))
            ->orderByDesc('fecha')
            ->get();
    }

    public function render(): View
    {
        return view('livewire.panel.videos.listado', [
            'videos' => $this->videosFiltrados(),
            'categorias' => Video::CATEGORIAS,
            'contadores' => [
                'cargados' => Video::query()->count(),
                'publicados' => Video::query()->where('publicado', true)->count(),
                'ocultos' => Video::query()->where('publicado', false)->count(),
                'destacados' => Video::query()->where('destacado', true)->count(),
            ],
        ]);
    }
}
