<?php

namespace App\Models;

use App\Enums\RolUsuario;
use App\Scopes\PerteneceAlCliente;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** @var list<string> */
    protected $fillable = ['name', 'email', 'password', 'rol', 'cliente_id', 'activo'];

    /** @var list<string> */
    protected $hidden = ['password', 'two_factor_secret'];

    /** @var array<string, string> */
    protected $casts = [
        'password' => 'hashed',
        'rol' => RolUsuario::class,
        'activo' => 'boolean',
        'ultimo_acceso_at' => 'datetime',
        // Cifrado en reposo: es un secreto TOTP, no un dato de exhibición.
        'two_factor_secret' => 'encrypted',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new PerteneceAlCliente);
    }

    /**
     * @return BelongsTo<Cliente, $this>
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function esAdmin(): bool
    {
        return $this->rol === RolUsuario::Admin;
    }

    public function esCliente(): bool
    {
        return $this->rol === RolUsuario::Cliente;
    }

    public function tieneSegundoFactorActivo(): bool
    {
        return ! is_null($this->two_factor_secret);
    }

    /**
     * Un cliente archivado no puede iniciar sesión (RF-22), y una cuenta
     * marcada como inactiva tampoco, sea cual sea su rol.
     */
    public function puedeIniciarSesion(): bool
    {
        if (! $this->activo) {
            return false;
        }

        if ($this->esCliente() && $this->cliente?->archivado) {
            return false;
        }

        return true;
    }
}
