<?php

namespace Database\Factories;

use App\Models\Jugador;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Jugador>
 */
class JugadorFactory extends Factory
{
    protected $model = Jugador::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->name(),
            'dni' => $this->faker->unique()->numberBetween(10000000, 99999999),
            'genero' => $this->faker->randomElement(['Masculino', 'Femenino']),
            'habilidad' => $this->faker->numberBetween(50, 100),
        ];
    }
}
