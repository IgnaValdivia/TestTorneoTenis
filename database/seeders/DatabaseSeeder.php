<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Jugador;
use App\Models\Partida;
use App\Models\Torneo;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Jugador::factory(10)->create(['genero' => 'Femenino']);
        Jugador::factory(10)->create(['genero' => 'Masculino']);
        Partida::factory(10)->create();
        Torneo::factory(10)->create();
    }
}
