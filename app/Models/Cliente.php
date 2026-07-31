<?php

namespace App\Models;

use Database\Factories\ClienteFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    /** @use HasFactory<ClienteFactory> */
    use HasFactory;

    protected $fillable = [
        'marca',
        'contacto',
        'correo_contacto',
        'telefono',
        'color',
        'archivado',
    ];

    protected function casts(): array
    {
        return [
            'archivado' => 'boolean',
        ];
    }

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return HasMany<AgendaEvento, $this>
     */
    public function agendaEventos(): HasMany
    {
        return $this->hasMany(AgendaEvento::class);
    }

    /**
     * @return HasMany<Publicacion, $this>
     */
    public function publicaciones(): HasMany
    {
        return $this->hasMany(Publicacion::class);
    }

    /**
     * @param  Builder<Cliente>  $query
     * @return Builder<Cliente>
     */
    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('archivado', false);
    }
}
