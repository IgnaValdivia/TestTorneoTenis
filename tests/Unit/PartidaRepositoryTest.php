<?php

namespace Tests\Unit;

use App\Models\Jugador;
use Tests\TestCase;
use App\Models\Partida;
use App\Models\Torneo;
use App\Repositories\PartidaRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class PartidaRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private PartidaRepository $partidaRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->partidaRepository = new PartidaRepository();
    }

    #[Test]
    public function puede_crear_una_partida()
    {
        $torneo = Torneo::factory()->create();
        $jugador1 = Jugador::factory()->create(['genero' => $torneo->tipo]);
        $jugador2 = Jugador::factory()->create(['genero' => $torneo->tipo]);

        $partida = $this->partidaRepository->create([
            'torneo_id' => $torneo->id,
            'jugador1_id' => $jugador1->id,
            'jugador2_id' => $jugador2->id,
            'ronda' => 1,
        ]);

        $this->assertDatabaseHas('partidas', ['id' => $partida->id]);
    }

    #[Test]
    public function puede_obtener_todas_las_partidas()
    {
        Partida::factory()->count(5)->create();
        $partidas = $this->partidaRepository->getAll();

        $this->assertCount(5, $partidas);
    }

    #[Test]
    public function puede_buscar_partida_por_id()
    {
        $partida = Partida::factory()->create();
        $encontrada = $this->partidaRepository->findById($partida->id);

        $this->assertNotNull($encontrada);
        $this->assertEquals($partida->id, $encontrada->id);
    }

    #[Test]
    public function puede_actualizar_una_partida()
    {
        $partida = Partida::factory()->create();
        $actualizada = $this->partidaRepository->update($partida->id, ['ronda' => 2]);

        $this->assertTrue($actualizada);
        $this->assertDatabaseHas('partidas', ['id' => $partida->id, 'ronda' => 2]);
    }

    #[Test]
    public function puede_eliminar_una_partida()
    {
        $partida = Partida::factory()->create();
        $eliminada = $this->partidaRepository->delete($partida->id);

        $this->assertTrue($eliminada);
        $this->assertDatabaseHas('partidas', ['id' => $partida->id]);

        //Verificar que está marcado como eliminado (SoftDeletes)
        $this->assertNotNull(Partida::withTrashed()->find($partida->id)->deleted_at);
    }

    #[Test]
    public function puede_eliminar_partidas_por_torneo()
    {
        $torneo = Torneo::factory()->create();
        Partida::factory()->count(3)->create(['torneo_id' => $torneo->id]);

        $eliminadas = $this->partidaRepository->deleteByTorneoId($torneo->id);

        $this->assertTrue($eliminadas);
        $this->assertDatabaseHas('partidas', ['torneo_id' => $torneo->id]);
    }
}
