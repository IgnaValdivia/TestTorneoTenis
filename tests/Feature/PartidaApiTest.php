<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Partida;
use App\Models\Torneo;
use App\Models\Jugador;
use PHPUnit\Framework\Attributes\Test;

class PartidaApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_puede_obtener_detalles_de_una_partida()
    {
        //Crear los datos de prueba
        $torneo = Torneo::factory()->create();
        $jugador1 = Jugador::factory()->create();
        $jugador2 = Jugador::factory()->create();

        $partida = Partida::factory()->create([
            'torneo_id' => $torneo->id,
            'jugador1_id' => $jugador1->id,
            'jugador2_id' => $jugador2->id,
            'ganador_id' => $jugador1->id,
            'ronda' => 1,
        ]);

        //Realizar la petición
        $response = $this->getJson(route('partidas.show', ['id' => $partida->id]));

        //Verificar respuesta (200 OK)
        $response->assertStatus(200)
            ->assertJson([
                'id' => $partida->id,
                'torneo_id' => $partida->torneo_id,
                'jugador1_id' => $partida->jugador1_id,
                'jugador2_id' => $partida->jugador2_id,
                'ganador_id' => $partida->ganador_id,
                'ronda' => $partida->ronda,
            ]);
    }

    #[Test]
    public function test_devuelve_404_si_la_partida_no_existe()
    {
        //Realizar la petición con un ID que no existe
        $response = $this->getJson(route('partidas.show', ['id' => 999]));

        //Verificar respuesta (404 Not Found)
        $response->assertStatus(404)
            ->assertJson([
                'error' => 'Partida no encontrada',
            ]);
    }
}
