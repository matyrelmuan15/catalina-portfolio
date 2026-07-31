<?php

namespace App\Models;

use App\Services\ProveedorVideo;
use Database\Factories\VideoFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Video extends Model
{
    /** @use HasFactory<VideoFactory> */
    use HasFactory;

    /**
     * Las seis categorías del filtro (docs/01-especificacion-funcional.md RF-03).
     */
    public const CATEGORIAS = ['UGC', 'Marketing', 'Lifestyle', 'Fotografía', 'Modelaje', 'Redes'];

    protected $fillable = [
        'titulo',
        'cliente_texto',
        'categoria',
        'fecha',
        'proveedor',
        'enlace',
        'miniatura_path',
        'descripcion',
        'publicado',
        'destacado',
        'orden',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'publicado' => 'boolean',
            'destacado' => 'boolean',
        ];
    }

    /**
     * @param  Builder<Video>  $query
     * @return Builder<Video>
     */
    public function scopePublicados(Builder $query): Builder
    {
        return $query->where('publicado', true);
    }

    /**
     * Orden de la grilla pública: destacados primero, luego por fecha
     * descendente (docs/01-especificacion-funcional.md RF-02).
     *
     * @param  Builder<Video>  $query
     * @return Builder<Video>
     */
    public function scopeOrdenDelPortfolio(Builder $query): Builder
    {
        return $query->orderByDesc('destacado')->orderByDesc('fecha')->orderByDesc('id');
    }

    public function urlEmbebida(): string
    {
        return app(ProveedorVideo::class)->urlEmbebida($this->proveedor, $this->enlace);
    }

    public function urlMiniatura(): ?string
    {
        if ($this->miniatura_path) {
            return Storage::disk('r2')->url($this->miniatura_path);
        }

        return app(ProveedorVideo::class)->urlMiniatura($this->proveedor, $this->enlace);
    }
}
