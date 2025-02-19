<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Torneo>
 */
class TorneoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $genero = $this->faker->randomElement(['Masculino', 'Femenino']);
        return [
            'nombre' => $this->faker->sentence(3),
            'tipo' => $genero,
            'estado' => 'Pendiente',
            'fecha' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'ganador_id' => null,
        ];
    }
}
