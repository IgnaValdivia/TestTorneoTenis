<?php

namespace Tests\Feature;

use App\Models\Jugador;
use Tests\TestCase;
use App\Services\TorneoService;
use App\Repositories\TorneoRepository;
use App\Repositories\PartidaRepository;
use App\Services\JugadorService;
use App\Models\Torneo;
use App\Models\Partida;
use App\Models\JugadorMasculino;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Exception;

class TorneoServiceTest extends TestCase
{
    use RefreshDatabase;

    private TorneoService $torneoService;

    protected function setUp(): void
    {
        parent::setUp();

        $torneoRepository = new TorneoRepository();
        $partidaRepository = new PartidaRepository();
        $jugadorService = $this->createMock(JugadorService::class);

        $this->torneoService = new TorneoService($torneoRepository, $partidaRepository, $jugadorService);
    }

    #[Test]
    public function puede_crear_un_torneo()
    {
        $data = [
            'nombre' => 'Torneo Test',
            'tipo' => 'Masculino',
            'fecha' => now(),
        ];

        $torneo = $this->torneoService->create($data);

        $this->assertDatabaseHas('torneos', ['id' => $torneo->id, 'estado' => 'Pendiente']);
        $this->assertEquals('Pendiente', $torneo->estado);
    }

    #[Test]
    public function puede_obtener_un_torneo_por_id()
    {
        $torneo = Torneo::factory()->create();
        $torneoEncontrado = $this->torneoService->findById($torneo->id);

        $this->assertNotNull($torneoEncontrado);
        $this->assertEquals($torneo->id, $torneoEncontrado->id);
    }

    #[Test]
    public function puede_actualizar_un_torneo()
    {
        $torneo = Torneo::factory()->create();

        $actualizado = $this->torneoService->update($torneo->id, ['nombre' => 'Torneo Actualizado']);

        $this->assertTrue($actualizado);
        $this->assertDatabaseHas('torneos', ['id' => $torneo->id, 'nombre' => 'Torneo Actualizado']);
    }

    #[Test]
    public function puede_eliminar_un_torneo()
    {
        $torneo = Torneo::factory()->create();
        $eliminado = $this->torneoService->delete($torneo->id);

        $this->assertTrue($eliminado);
        $this->assertSoftDeleted('torneos', ['id' => $torneo->id]);
    }

    #[Test]
    public function puede_restaurar_un_torneo()
    {
        $torneo = Torneo::factory()->create();
        $torneo->delete();

        $restaurado = $this->torneoService->restore($torneo->id);

        $this->assertTrue($restaurado);
        $this->assertDatabaseHas('torneos', ['id' => $torneo->id]);
    }

    /*#[Test]
    public function puede_asignar_jugadores_a_un_torneo()
    {
        $torneo = Torneo::factory()->create();
        $jugadores = Jugador::factory()->count(3)->create()->pluck('id')->toArray();

        echo var_dump($jugadores);
        $asignado = $this->torneoService->asignarJugadores($torneo->id, $jugadores);

        $this->assertTrue($asignado);
        $this->assertEquals(3, $torneo->jugadores()->count());
    }*/

    #[Test]
    public function puede_determinar_ganador_de_una_partida()
    {
        $torneo = Torneo::factory()->create();
        $jugador1 = Jugador::factory()->create(['genero' => $torneo->tipo]);
        $jugador2 = Jugador::factory()->create(['genero' => $torneo->tipo]);

        $partida = Partida::factory()->create([
            'torneo_id' => $torneo->id,
            'jugador1_id' => $jugador1->id,
            'jugador2_id' => $jugador2->id,
        ]);

        $ganador = $this->torneoService->determinarGanador($partida);

        $this->assertNotNull($ganador);
        $this->assertDatabaseHas('partidas', ['id' => $partida->id, 'ganador_id' => $ganador->id]);
    }

    #[Test]
    public function puede_actualizar_el_ganador_de_un_torneo()
    {
        $torneo = Torneo::factory()->create();
        $jugadorGanador = JugadorMasculino::factory()->create();

        $actualizado = $this->torneoService->actualizarGanador($torneo->id, $jugadorGanador->id);

        $this->assertTrue($actualizado);
        $this->assertDatabaseHas('torneos', ['id' => $torneo->id, 'ganador_id' => $jugadorGanador->id, 'estado' => 'Finalizado']);
    }

    #[Test]
    public function puede_obtener_partidas_de_un_torneo()
    {
        $torneo = Torneo::factory()->create();
        Partida::factory()->count(5)->create(['torneo_id' => $torneo->id]);

        $partidas = $this->torneoService->getPartidas($torneo->id);

        $this->assertCount(5, $partidas);
    }

    #[Test]
    public function puede_obtener_estado_de_un_torneo()
    {
        $torneo = Torneo::factory()->create();

        $estado = $this->torneoService->getEstado($torneo->id);

        $this->assertEquals('Pendiente', $estado);
    }
}
