<?php

namespace Tests\Unit;

use App\Models\Jugador;
use App\Models\Partida;
use App\Models\Torneo;
use App\Repositories\TorneoRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class TorneoRepositoryTest extends TestCase
{
    //Se asegura de que la base de datos se reinicie en cada prueba
    use RefreshDatabase;

    private TorneoRepository $torneoRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->torneoRepository = new TorneoRepository();
    }

    #[Test]
    public function puede_crear_un_torneo()
    {
        $data = [
            'nombre' => 'Torneo Test',
            'tipo' => 'Masculino',
            'estado' => 'Pendiente',
            'fecha' => now(),
        ];

        $torneo = $this->torneoRepository->create($data);

        $this->assertDatabaseHas('torneos', ['nombre' => 'Torneo Test']);
        $this->assertInstanceOf(Torneo::class, $torneo);
    }

    #[Test]
    public function puede_obtener_todos_los_torneos()
    {
        Torneo::factory()->count(3)->create();

        $torneos = $this->torneoRepository->getAll();

        $this->assertCount(3, $torneos);
    }

    #[Test]
    public function puede_buscar_torneo_por_id()
    {
        $torneo = Torneo::factory()->create();

        $torneoEncontrado = $this->torneoRepository->findById($torneo->id);

        $this->assertNotNull($torneoEncontrado);
        $this->assertEquals($torneo->id, $torneoEncontrado->id);
    }

    #[Test]
    public function puede_actualizar_un_torneo()
    {
        $torneo = Torneo::factory()->create();

        $actualizado = $this->torneoRepository->update($torneo->id, ['nombre' => 'Nuevo Nombre']);

        $this->assertTrue($actualizado);
        $this->assertDatabaseHas('torneos', ['nombre' => 'Nuevo Nombre']);
    }

    #[Test]
    public function puede_eliminar_un_torneo()
    {
        $torneo = Torneo::factory()->create();

        $eliminado = $this->torneoRepository->delete($torneo->id);

        $this->assertTrue($eliminado);

        $this->assertDatabaseHas('torneos', ['id' => $torneo->id]);

        $this->assertSoftDeleted('torneos', ['id' => $torneo->id]);
    }

    #[Test]
    public function puede_restaurar_un_torneo()
    {
        $torneo = Torneo::factory()->create();
        $this->torneoRepository->delete($torneo->id);

        $restaurado = $this->torneoRepository->restore($torneo->id);

        $this->assertTrue($restaurado);
        $this->assertDatabaseHas('torneos', ['id' => $torneo->id]);

        //Verificar que está marcado como eliminado (SoftDeletes)
        $this->assertNull(Torneo::find($torneo->id)->deleted_at);
    }

    #[Test]
    public function puede_obtener_partidas_de_un_torneo()
    {
        $torneo = Torneo::factory()->create();
        Partida::factory()->count(2)->create(['torneo_id' => $torneo->id]);

        $partidas = $this->torneoRepository->getPartidas($torneo->id);

        $this->assertCount(2, $partidas);
    }

    #[Test]
    public function puede_asignar_jugadores_a_un_torneo()
    {
        $torneo = Torneo::factory()->create();
        $jugadores = Jugador::factory()->count(3)->create()->pluck('id')->toArray();

        $this->torneoRepository->asignarJugadores($torneo->id, $jugadores);

        foreach ($jugadores as $jugadorId) {
            $this->assertDatabaseHas('torneo_jugador', [
                'torneo_id' => $torneo->id,
                'jugador_id' => $jugadorId,
            ]);
        }
    }

    #[Test]
    public function puede_obtener_estado_de_un_torneo()
    {
        $torneo = Torneo::factory()->create();

        $estado = $this->torneoRepository->getEstado($torneo->id);

        $this->assertEquals('Pendiente', $estado);
    }

    #[Test]
    public function puede_obtener_partidas_por_ronda()
    {
        $torneo = Torneo::factory()->create();
        Partida::factory()->count(2)->create(['torneo_id' => $torneo->id, 'ronda' => 1]);

        $partidas = $this->torneoRepository->getPartidasPorRonda($torneo->id, 1);

        $this->assertCount(2, $partidas);
    }
}
