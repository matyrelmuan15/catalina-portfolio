<?php

namespace Database\Factories;

use App\Models\Publicacion;
use App\Models\PublicacionMetrica;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PublicacionMetrica>
 */
class PublicacionMetricaFactory extends Factory
{
    protected $model = PublicacionMetrica::class;

    public function definition(): array
    {
        $alcance = fake()->numberBetween(5000, 60000);
        $interacciones = (int) round($alcance * fake()->randomFloat(3, 0.02, 0.09));

        return [
            'publicacion_id' => Publicacion::factory(),
            'medido_el' => now()->toDateString(),
            'alcance' => $alcance,
            'vistas' => $alcance + fake()->numberBetween(500, 8000),
            'interacciones' => $interacciones,
            'me_gusta' => (int) round($interacciones * 0.75),
            'comentarios' => fake()->numberBetween(10, 150),
            'compartidos' => fake()->numberBetween(20, 400),
            'guardados' => fake()->numberBetween(15, 300),
            'clics_enlace' => null,
            'seguidores_al_publicar' => fake()->numberBetween(2000, 25000),
            'tasa_interaccion' => null,
        ];
    }
}
