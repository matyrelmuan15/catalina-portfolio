<?php

namespace App\Models;

use App\Concerns\PerteneceACliente;
use Database\Factories\AgendaEventoFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgendaEvento extends Model
{
    /** @use HasFactory<AgendaEventoFactory> */
    use HasFactory, PerteneceACliente;

    protected $table = 'agenda_eventos';

    /**
     * Los tres tipos de evento (docs/01-especificacion-funcional.md §6.1).
     * "Publicación" no es un tipo de evento: es una entidad propia (fase 6).
     */
    public const TIPOS = ['grabacion', 'entrega', 'reunion'];

    protected $fillable = [
        'cliente_id',
        'fecha',
        'tipo',
        'titulo',
        'nota',
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
     * @param  Builder<AgendaEvento>  $query
     * @return Builder<AgendaEvento>
     */
    public function scopeProximos(Builder $query): Builder
    {
        return $query->where('fecha', '>=', now()->toDateString())->orderBy('fecha');
    }

    public function etiquetaTipo(): string
    {
        return match ($this->tipo) {
            'grabacion' => 'Grabación',
            'entrega' => 'Entrega',
            'reunion' => 'Reunión',
            default => $this->tipo,
        };
    }

    /**
     * Usados por resources/views/components/calendario-grilla.blade.php,
     * que muestra en la misma grilla eventos de agenda y publicaciones
     * (docs/01-especificacion-funcional.md §6.1).
     */
    public function tipoCss(): string
    {
        return $this->tipo;
    }

    public function esEditable(): bool
    {
        return true;
    }
}
