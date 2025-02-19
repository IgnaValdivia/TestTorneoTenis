<?php

namespace Database\Factories;

use App\Models\Jugador;
use App\Models\JugadorFemenino;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JugadorFemenino>
 */
class JugadorFemeninoFactory extends Factory
{
    protected $model = JugadorFemenino::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Jugador::factory()->create(['genero' => 'Femenino'])->id,
            'reaccion' => $this->faker->numberBetween(0, 100),
        ];
    }
}
