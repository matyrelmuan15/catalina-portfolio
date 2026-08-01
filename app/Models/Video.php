<?php

namespace App\Models;

use App\Enums\CategoriaVideo;
use App\Enums\ProveedorVideoTipo;
use Database\Factories\VideoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    /** @use HasFactory<VideoFactory> */
    use HasFactory;

    private const PALETA_TONOS = ['fucsia', 'rosa', 'ciruela', 'arena', 'coral', 'nocturno'];

    /** @var list<string> */
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

    /** @var array<string, string> */
    protected $casts = [
        'categoria' => CategoriaVideo::class,
        'fecha' => 'date',
        'proveedor' => ProveedorVideoTipo::class,
        'publicado' => 'boolean',
        'destacado' => 'boolean',
        'orden' => 'integer',
    ];

    /**
     * Clase de color decorativa cuando la pieza no tiene miniatura propia
     * (RF-10: "si no hay, se usa la del proveedor"; hasta que esa miniatura
     * se resuelve, o para el archivo, se usa un color estable por id).
     */
    public function tonoClase(): string
    {
        return 'tono-'.self::PALETA_TONOS[$this->id % count(self::PALETA_TONOS)];
    }
}
