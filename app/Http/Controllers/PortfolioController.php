<?php

namespace App\Http\Controllers;

use App\Enums\CategoriaVideo;
use App\Models\Video;
use App\Services\ProveedorVideo;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;

class PortfolioController extends Controller
{
    public function index(ProveedorVideo $proveedorVideo): View
    {
        $videos = Video::query()
            ->where('publicado', true)
            ->orderByDesc('destacado')
            ->orderByDesc('fecha')
            ->get();

        $miniaturas = $videos->mapWithKeys(fn (Video $video) => [
            $video->id => $this->miniatura($video, $proveedorVideo),
        ]);

        $embebidos = $videos->mapWithKeys(fn (Video $video) => [
            $video->id => $proveedorVideo->urlEmbebido($video->enlace),
        ]);

        return view('publico.portfolio', [
            'videos' => $videos,
            'categorias' => CategoriaVideo::cases(),
            'miniaturas' => $miniaturas,
            'embebidos' => $embebidos,
        ]);
    }

    private function miniatura(Video $video, ProveedorVideo $proveedorVideo): ?string
    {
        if ($video->miniatura_path) {
            return Storage::disk('r2_publico')->url($video->miniatura_path);
        }

        return $proveedorVideo->urlMiniatura($video->enlace);
    }
}
