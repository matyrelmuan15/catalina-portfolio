<?php

namespace App\Models;

use Database\Factories\PublicacionMetricaFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicacionMetrica extends Model
{
    /** @use HasFactory<PublicacionMetricaFactory> */
    use HasFactory;

    protected $table = 'publicacion_metricas';

    protected $fillable = [
        'publicacion_id',
        'medido_el',
        'alcance',
        'vistas',
        'interacciones',
        'me_gusta',
        'comentarios',
        'compartidos',
        'guardados',
        'clics_enlace',
        'seguidores_al_publicar',
        'tasa_interaccion',
    ];

    protected function casts(): array
    {
        return [
            'medido_el' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Publicacion, $this>
     */
    public function publicacion(): BelongsTo
    {
        return $this->belongsTo(Publicacion::class);
    }

    /**
     * RF-45: interacciones ÷ alcance × 100 cuando no viene calculada en el
     * archivo importado. Nunca devuelve un número si falta el dato base:
     * un cero se leería como un resultado (docs/01 §7.4).
     */
    /**
     * @return Attribute<string|null, never>
     */
    protected function tasaInteraccion(): Attribute
    {
        return Attribute::make(
            get: function ($valor) {
                if ($valor !== null) {
                    return $valor;
                }

                if (! $this->alcance) {
                    return null;
                }

                return round((($this->interacciones ?? 0) / $this->alcance) * 100, 2);
            },
        );
    }
}
