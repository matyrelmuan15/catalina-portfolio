<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Publicacion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Publicacion>
 */
class PublicacionFactory extends Factory
{
    protected $model = Publicacion::class;

    public function definition(): array
    {
        return [
            'cliente_id' => Cliente::factory(),
            'titulo' => ucfirst(fake()->words(3, true)),
            'estado' => fake()->randomElement(Publicacion::ESTADOS_MANUALES),
            'pilar' => fake()->randomElement(['Producto', 'Educativo', 'Detrás de escena', 'Testimonio', 'Promoción']),
            'fecha' => fake()->dateTimeBetween('-2 months', '+1 month'),
            'plataforma' => fake()->randomElement(Publicacion::PLATAFORMAS),
            'formato' => fake()->randomElement(Publicacion::FORMATOS),
        ];
    }

    public function medida(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'Medida',
            'id_media' => (string) fake()->unique()->numerify('179#############'),
            'permalink' => 'https://www.instagram.com/p/'.fake()->unique()->bothify('??????????').'/',
            'copy_texto' => fake()->sentence(12),
            'hashtags' => '#'.implode(' #', fake()->words(3)),
        ]);
    }
}
