<?php

namespace Database\Factories;

use App\Enums\CategoriaVideo;
use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Video>
 */
class VideoFactory extends Factory
{
    protected $model = Video::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'titulo' => ucfirst(fake()->words(3, true)),
            'cliente_texto' => fake()->company(),
            'categoria' => fake()->randomElement(CategoriaVideo::cases())->value,
            'fecha' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'proveedor' => 'vimeo',
            'enlace' => 'https://vimeo.com/'.fake()->unique()->numberBetween(100000, 999999),
            'miniatura_path' => null,
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
