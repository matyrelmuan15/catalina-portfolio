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

    public function definition(): array
    {
        $colores = ['fucsia', 'rosa', 'ciruela', 'arena', 'coral', 'nocturno'];

        return [
            'marca' => fake()->unique()->company(),
            'contacto' => fake()->name(),
            'correo_contacto' => fake()->unique()->companyEmail(),
            'telefono' => fake()->phoneNumber(),
            'color' => fake()->randomElement($colores),
            'archivado' => false,
        ];
    }

    public function archivado(): static
    {
        return $this->state(fn (array $attributes) => ['archivado' => true]);
    }
}
