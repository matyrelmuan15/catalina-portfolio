<?php

namespace App\Models;

use App\Concerns\PerteneceACliente;
use Database\Factories\PublicacionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Publicacion extends Model
{
    /** @use HasFactory<PublicacionFactory> */
    use HasFactory, PerteneceACliente;

    protected $table = 'publicaciones';

    /**
     * docs/01-especificacion-funcional.md §7.3. "Medida" solo lo pone el
     * servicio de importación (fase 7) cuando llegan métricas: no está en
     * las opciones manuales del formulario.
     */
    public const ESTADOS = ['Planificada', 'En producción', 'Aprobada', 'Publicada', 'Medida'];

    public const ESTADOS_MANUALES = ['Planificada', 'En producción', 'Aprobada', 'Publicada'];

    public const PLATAFORMAS = ['instagram', 'facebook'];

    public const FORMATOS = ['reel', 'carrusel', 'imagen', 'historia', 'video'];

    protected $fillable = [
        'cliente_id',
        'titulo',
        'estado',
        'pilar',
        'archivo_final_url',
        'creativo_figma_url',
        'fecha',
        'plataforma',
        'formato',
        'copy_texto',
        'hashtags',
        'id_media',
        'permalink',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Cliente, $this>
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * @return HasMany<PublicacionMetrica, $this>
     */
    public function metricas(): HasMany
    {
        return $this->hasMany(PublicacionMetrica::class)->orderByDesc('medido_el');
    }

    /**
     * @return HasOne<PublicacionMetrica, $this>
     */
    public function ultimaMetrica(): HasOne
    {
        return $this->hasOne(PublicacionMetrica::class)->latestOfMany('medido_el');
    }

    /**
     * Usados por resources/views/components/calendario-grilla.blade.php:
     * las publicaciones aparecen en el calendario del cliente en su fecha,
     * pero no son un evento de agenda y no se editan desde ahí (RF-43).
     */
    public function tipoCss(): string
    {
        return 'publicacion';
    }

    public function etiquetaTipo(): string
    {
        return 'Publicación';
    }

    public function esEditable(): bool
    {
        return false;
    }

    /**
     * Sufijo de clase CSS para la insignia de estado
     * (mockup/portfolio-mockup.html: .insignia.planificada, .produccion, etc.).
     */
    public function claseEstado(): string
    {
        return match ($this->estado) {
            'Planificada' => 'planificada',
            'En producción' => 'produccion',
            'Aprobada' => 'aprobada',
            'Publicada' => 'publicada',
            'Medida' => 'medida',
            default => 'planificada',
        };
    }
}
