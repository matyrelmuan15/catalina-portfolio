<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cliente>
 */
class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    private const TONOS = ['fucsia', 'rosa', 'ciruela', 'arena', 'coral', 'nocturno'];

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'marca' => fake()->unique()->company(),
            'contacto' => fake()->name(),
            'correo_contacto' => fake()->unique()->safeEmail(),
            'telefono' => fake()->numerify('+54 9 2931 ##-####'),
            'color' => fake()->randomElement(self::TONOS),
            'archivado' => false,
        ];
    }

    public function archivado(): static
    {
        return $this->state(fn (array $attributes) => [
            'archivado' => true,
        ]);
    }
}
