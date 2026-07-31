<?php

namespace Database\Factories;

use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Video>
 */
class VideoFactory extends Factory
{
    protected $model = Video::class;

    public function definition(): array
    {
        return [
            'titulo' => ucfirst(fake()->words(3, true)),
            'cliente_texto' => fake()->company(),
            'categoria' => fake()->randomElement(Video::CATEGORIAS),
            'fecha' => fake()->dateTimeBetween('-1 year', 'now'),
            'proveedor' => 'vimeo',
            'enlace' => 'https://vimeo.com/'.fake()->numberBetween(100000000, 999999999),
            'descripcion' => fake()->sentence(18),
            'publicado' => true,
            'destacado' => false,
            'orden' => 0,
        ];
    }

    public function destacado(): static
    {
        return $this->state(fn (array $attributes) => ['destacado' => true]);
    }

    public function oculto(): static
    {
        return $this->state(fn (array $attributes) => ['publicado' => false]);
    }
}
