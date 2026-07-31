<?php

namespace Database\Factories;

use App\Models\AgendaEvento;
use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AgendaEvento>
 */
class AgendaEventoFactory extends Factory
{
    protected $model = AgendaEvento::class;

    public function definition(): array
    {
        return [
            'cliente_id' => Cliente::factory(),
            'fecha' => fake()->dateTimeBetween('-1 month', '+2 months'),
            'tipo' => fake()->randomElement(AgendaEvento::TIPOS),
            'titulo' => ucfirst(fake()->words(3, true)),
            'nota' => fake()->optional()->sentence(),
        ];
    }
}
