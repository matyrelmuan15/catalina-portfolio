<?php

namespace Database\Factories;

use App\Enums\RolUsuario;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * La clave que usa la factory por defecto, para no rehashear en cada fila.
     */
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'rol' => RolUsuario::Cliente,
            'cliente_id' => Cliente::factory(),
            'activo' => true,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol' => RolUsuario::Admin,
            'cliente_id' => null,
        ]);
    }

    public function inactivo(): static
    {
        return $this->state(fn (array $attributes) => [
            'activo' => false,
        ]);
    }
}
