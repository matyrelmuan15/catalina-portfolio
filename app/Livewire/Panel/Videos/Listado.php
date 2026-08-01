<?php

namespace App\Livewire\Panel\Videos;

use App\Enums\CategoriaVideo;
use App\Models\Video;
use App\Services\ProcesadorMiniatura;
use App\Services\ProveedorVideo;
use App\Services\PurgaCacheCloudflare;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.panel', ['titulo' => 'Videos'])]
class Listado extends Component
{
    use WithFileUploads;

    public string $busqueda = '';

    public string $categoriaFiltro = 'Todas las categorías';

    public bool $formularioAbierto = false;

    public ?int $editandoId = null;

    public string $titulo = '';

    public string $clienteTexto = '';

    public string $categoria = 'UGC';

    public string $fecha = '';

    public string $enlace = '';

    public string $descripcion = '';

    public bool $publicado = true;

    public bool $destacado = false;

    public mixed $miniatura = null;

    public ?int $confirmandoEliminarId = null;

    public function mount(): void
    {
        $this->fecha = now()->format('Y-m-d');
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:120'],
            'clienteTexto' => ['required', 'string', 'max:120'],
            'categoria' => ['required', Rule::in(array_column(CategoriaVideo::cases(), 'value'))],
            'fecha' => ['required', 'date'],
            'enlace' => ['required', 'url', 'max:500'],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'miniatura' => ['nullable', 'image', 'max:8192'],
        ];
    }

    public function nuevo(): void
    {
        $this->resetearFormulario();
        $this->formularioAbierto = true;
    }

    public function editar(int $id): void
    {
        $video = Video::findOrFail($id);

        $this->editandoId = $video->id;
        $this->titulo = $video->titulo;
        $this->clienteTexto = $video->cliente_texto;
        $this->categoria = $video->categoria->value;
        $this->fecha = $video->fecha->format('Y-m-d');
        $this->enlace = $video->enlace;
        $this->descripcion = (string) $video->descripcion;
        $this->publicado = $video->publicado;
        $this->destacado = $video->destacado;
        $this->miniatura = null;
        $this->resetValidation();
        $this->formularioAbierto = true;
    }

    public function cancelar(): void
    {
        $this->formularioAbierto = false;
        $this->resetearFormulario();
    }

    public function guardar(ProcesadorMiniatura $procesador, ProveedorVideo $proveedorVideo, PurgaCacheCloudflare $purgador): void
    {
        $this->validate();

        $datos = [
            'titulo' => $this->titulo,
            'cliente_texto' => $this->clienteTexto,
            'categoria' => $this->categoria,
            'fecha' => $this->fecha,
            'enlace' => $this->enlace,
            'descripcion' => $this->descripcion !== '' ? $this->descripcion : null,
            'publicado' => $this->publicado,
            'destacado' => $this->destacado,
            'proveedor' => $proveedorVideo->detectar($this->enlace)->value,
        ];

        if ($this->miniatura) {
            $binario = $procesador->procesar($this->miniatura->getRealPath());
            $ruta = 'videos/'.Str::uuid()->toString().'.webp';
            Storage::disk('r2_publico')->put($ruta, $binario, 'public');
            $datos['miniatura_path'] = $ruta;
        }

        if ($this->editandoId) {
            $video = Video::findOrFail($this->editandoId);
            $cambioPublicado = $video->publicado !== $this->publicado;
            $video->update($datos);
            $mensaje = 'Cambios guardados';
        } else {
            $video = Video::create($datos);
            $cambioPublicado = $this->publicado;
            $mensaje = 'Video subido';
        }

        if ($cambioPublicado) {
            $purgador->purgarPortada();
        }

        $this->formularioAbierto = false;
        $this->resetearFormulario();
        $this->dispatch('aviso', mensaje: $mensaje);
    }

    public function alternarPublicado(int $id, PurgaCacheCloudflare $purgador): void
    {
        $video = Video::findOrFail($id);
        $video->update(['publicado' => ! $video->publicado]);
        $purgador->purgarPortada();

        $this->dispatch('aviso', mensaje: $video->publicado ? 'Publicado en el sitio' : 'Oculto del sitio');
    }

    public function alternarDestacado(int $id): void
    {
        $video = Video::findOrFail($id);
        $video->update(['destacado' => ! $video->destacado]);

        $this->dispatch('aviso', mensaje: $video->destacado ? 'Marcado como destacado' : 'Ya no es destacado');
    }

    public function confirmarEliminar(int $id): void
    {
        $this->confirmandoEliminarId = $id;
    }

    public function cancelarEliminar(): void
    {
        $this->confirmandoEliminarId = null;
    }

    public function eliminar(PurgaCacheCloudflare $purgador): void
    {
        $video = Video::findOrFail($this->confirmandoEliminarId);
        $eraPublicado = $video->publicado;

        if ($video->miniatura_path) {
            Storage::disk('r2_publico')->delete($video->miniatura_path);
        }

        $video->delete();
        $this->confirmandoEliminarId = null;

        if ($eraPublicado) {
            $purgador->purgarPortada();
        }

        $this->dispatch('aviso', mensaje: 'Video eliminado');
    }

    public function render(ProveedorVideo $proveedorVideo): View
    {
        $termino = trim($this->busqueda);

        $videos = Video::query()
            ->when($termino !== '', fn ($query) => $query->where(
                fn ($sub) => $sub
                    ->whereLike('titulo', "%{$termino}%", caseSensitive: false)
                    ->orWhereLike('cliente_texto', "%{$termino}%", caseSensitive: false)
            ))
            ->when(
                $this->categoriaFiltro !== 'Todas las categorías',
                fn ($query) => $query->where('categoria', $this->categoriaFiltro)
            )
            ->orderByDesc('fecha')
            ->get();

        $miniaturas = $videos->mapWithKeys(fn (Video $video) => [
            $video->id => $video->miniatura_path
                ? Storage::disk('r2_publico')->url($video->miniatura_path)
                : $proveedorVideo->urlMiniatura($video->enlace),
        ]);

        return view('livewire.panel.videos.listado', [
            'videos' => $videos,
            'miniaturas' => $miniaturas,
            'categorias' => CategoriaVideo::cases(),
            'totalCargados' => Video::count(),
            'totalPublicados' => Video::where('publicado', true)->count(),
            'totalOcultos' => Video::where('publicado', false)->count(),
            'totalDestacados' => Video::where('destacado', true)->count(),
        ]);
    }

    private function resetearFormulario(): void
    {
        $this->editandoId = null;
        $this->titulo = '';
        $this->clienteTexto = '';
        $this->categoria = CategoriaVideo::Ugc->value;
        $this->fecha = now()->format('Y-m-d');
        $this->enlace = '';
        $this->descripcion = '';
        $this->publicado = true;
        $this->destacado = false;
        $this->miniatura = null;
        $this->resetValidation();
    }
}
