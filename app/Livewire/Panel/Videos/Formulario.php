<?php

namespace App\Livewire\Panel\Videos;

use App\Models\Video;
use App\Services\ProcesadorMiniatura;
use App\Services\ProveedorVideo;
use App\Services\PurgadorCacheCloudflare;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

/**
 * RF-10 y RF-42: alta y edición de un video del portfolio. Vive dentro del
 * modal que abre App\Livewire\Panel\Videos\Listado.
 */
class Formulario extends Component
{
    use WithFileUploads;

    public ?int $videoId = null;

    #[Validate('required|string|max:120')]
    public string $titulo = '';

    #[Validate('required|string|max:120')]
    public string $cliente_texto = '';

    #[Validate('required|string|in:UGC,Marketing,Lifestyle,Fotografía,Modelaje,Redes')]
    public string $categoria = '';

    #[Validate('required|date')]
    public string $fecha = '';

    #[Validate('required|url|max:300')]
    public string $enlace = '';

    #[Validate('nullable|string|max:500')]
    public string $descripcion = '';

    public bool $publicado = true;

    public bool $destacado = false;

    /** @var TemporaryUploadedFile|null */
    #[Validate('nullable|image|max:5120')]
    public $miniatura = null;

    public ?string $miniaturaActualUrl = null;

    public function mount(?int $videoId = null): void
    {
        $this->videoId = $videoId;

        if ($videoId) {
            $video = Video::findOrFail($videoId);
            $this->titulo = $video->titulo;
            $this->cliente_texto = $video->cliente_texto;
            $this->categoria = $video->categoria;
            $this->fecha = Carbon::parse($video->fecha)->toDateString();
            $this->enlace = $video->enlace;
            $this->descripcion = (string) $video->descripcion;
            $this->publicado = $video->publicado;
            $this->destacado = $video->destacado;
            $this->miniaturaActualUrl = $video->urlMiniatura();
        }
    }

    public function guardar(): void
    {
        $this->validate();

        $video = $this->videoId ? Video::findOrFail($this->videoId) : new Video;

        $video->fill([
            'titulo' => $this->titulo,
            'cliente_texto' => $this->cliente_texto,
            'categoria' => $this->categoria,
            'fecha' => $this->fecha,
            'proveedor' => app(ProveedorVideo::class)->detectarProveedor($this->enlace),
            'enlace' => $this->enlace,
            'descripcion' => $this->descripcion,
            'publicado' => $this->publicado,
            'destacado' => $this->destacado,
        ]);

        if ($this->miniatura) {
            $procesador = app(ProcesadorMiniatura::class);
            $procesador->eliminar($video->miniatura_path);
            $video->miniatura_path = $procesador->procesarYGuardar($this->miniatura);
        }

        $video->save();

        app(PurgadorCacheCloudflare::class)->purgarPortada();

        $this->dispatch('video-guardado');
    }

    public function render(): View
    {
        return view('livewire.panel.videos.formulario');
    }
}
