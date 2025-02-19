<?php

namespace Database\Factories;

use App\Models\Jugador;
use App\Models\JugadorMasculino;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JugadorMasculino>
 */
class JugadorMasculinoFactory extends Factory
{
    protected $model = JugadorMasculino::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Jugador::factory()->create(['genero' => 'Masculino'])->id,
            'fuerza' => $this->faker->numberBetween(0, 100),
            'velocidad' => $this->faker->numberBetween(0, 100),
        ];
    }
}
