<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Jugador;
use App\Models\Torneo;
use App\Models\Partida;
use App\Repositories\JugadorRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class JugadorRepositoryTest extends TestCase
{
    use RefreshDatabase; // Para reiniciar la BD en cada test

    private JugadorRepository $jugadorRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->jugadorRepository = new JugadorRepository();
    }

    #[Test]
    public function puede_crear_un_jugador()
    {
        $data = [
            'nombre' => 'Carlos Pérez',
            'dni' => '12345678',
            'genero' => 'Masculino',
            'habilidad' => 80
        ];

        $jugador = $this->jugadorRepository->create($data);

        $this->assertInstanceOf(Jugador::class, $jugador);
        $this->assertDatabaseHas('jugadores', $data);
    }

    #[Test]
    public function puede_actualizar_un_jugador()
    {
        $jugador = Jugador::factory()->create();
        $nuevosDatos = ['nombre' => 'Nuevo Nombre'];

        $actualizado = $this->jugadorRepository->update($jugador->id, $nuevosDatos);

        $this->assertTrue($actualizado);
        $this->assertDatabaseHas('jugadores', ['id' => $jugador->id, 'nombre' => 'Nuevo Nombre']);
    }

    #[Test]
    public function puede_buscar_un_jugador_por_id()
    {
        $jugador = Jugador::factory()->create();

        $encontrado = $this->jugadorRepository->findById($jugador->id);

        $this->assertNotNull($encontrado);
        $this->assertEquals($jugador->id, $encontrado->id);
    }

    #[Test]
    public function puede_buscar_un_jugador_por_dni()
    {
        $jugador = Jugador::factory()->create(['dni' => '87654321']);

        $encontrado = $this->jugadorRepository->findByDni('87654321');

        $this->assertNotNull($encontrado);
        $this->assertEquals($jugador->id, $encontrado->id);
    }

    #[Test]
    public function puede_eliminar_un_jugador()
    {
        $jugador = Jugador::factory()->create();

        $eliminado = $this->jugadorRepository->delete($jugador->id);

        $this->assertTrue($eliminado);
        $this->assertSoftDeleted('jugadores', ['id' => $jugador->id]);
    }

    #[Test]
    public function puede_restaurar_un_jugador()
    {
        $jugador = Jugador::factory()->create();
        $jugador->delete();

        $restaurado = $this->jugadorRepository->restore($jugador->id);

        $this->assertTrue($restaurado);
        $this->assertDatabaseHas('jugadores', ['id' => $jugador->id, 'deleted_at' => null]);
    }

    #[Test]
    public function puede_obtener_los_torneos_de_un_jugador()
    {
        $jugador = Jugador::factory()->create();
        $torneo = Torneo::factory()->create();
        $torneo->jugadores()->attach($jugador->id);

        $torneos = $this->jugadorRepository->getTorneos($jugador->id);

        $this->assertCount(1, $torneos);
        $this->assertEquals($torneo->id, $torneos->first()->id);
    }

    #[Test]
    public function puede_obtener_las_partidas_de_un_jugador()
    {
        $jugador1 = Jugador::factory()->create();
        $jugador2 = Jugador::factory()->create();
        $partida = Partida::factory()->create([
            'jugador1_id' => $jugador1->id,
            'jugador2_id' => $jugador2->id,
        ]);

        $partidas = $this->jugadorRepository->getPartidas($jugador1->id);

        $this->assertCount(1, $partidas);
        $this->assertEquals($partida->id, $partidas->first()->id);
    }
}
