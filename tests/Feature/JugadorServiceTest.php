<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\JugadorService;
use App\Services\JugadorMasculinoService;
use App\Services\JugadorFemeninoService;
use App\Repositories\JugadorRepository;
use App\Repositories\JugadorMasculinoRepository;
use App\Repositories\JugadorFemeninoRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Jugador;
use App\Models\Partida;
use App\Models\Torneo;
use PHPUnit\Framework\Attributes\Test;
use Exception;

class JugadorServiceTest extends TestCase
{
    use RefreshDatabase;

    private JugadorService $jugadorService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jugadorService = new JugadorService(
            new JugadorRepository(),
            new JugadorMasculinoService(new JugadorRepository(), new JugadorMasculinoRepository()),
            new JugadorFemeninoService(new JugadorRepository(), new JugadorFemeninoRepository())
        );
    }

    #[Test]
    public function puede_crear_un_jugador_masculino()
    {
        $data = [
            'nombre' => 'Carlos Pérez',
            'dni' => '87654321',
            'genero' => 'Masculino',
            'habilidad' => 80,
            'fuerza' => 85,
            'velocidad' => 90,
        ];

        $jugadorDTO = $this->jugadorService->create('Masculino', $data);

        $this->assertDatabaseHas('jugadores', ['dni' => '87654321']);
        $this->assertDatabaseHas('jugadores_masculinos', ['id' => $jugadorDTO->id]);
    }

    #[Test]
    public function puede_crear_un_jugador_femenino()
    {
        $data = [
            'nombre' => 'Ana Gómez',
            'dni' => '12345678',
            'genero' => 'Femenino',
            'habilidad' => 75,
            'reaccion' => 80,
        ];

        $jugadorDTO = $this->jugadorService->create('Femenino', $data);

        $this->assertDatabaseHas('jugadores', ['dni' => '12345678']);
        $this->assertDatabaseHas('jugadores_femeninos', ['id' => $jugadorDTO->id]);
    }

    #[Test]
    public function puede_obtener_jugador_por_id()
    {
        $jugador = Jugador::factory()->create();
        $jugadorDTO = $this->jugadorService->findById($jugador->id);

        $this->assertEquals($jugador->nombre, $jugadorDTO->nombre);
        $this->assertEquals($jugador->dni, $jugadorDTO->dni);
    }

    #[Test]
    public function puede_obtener_jugador_por_dni()
    {
        $jugador = Jugador::factory()->create(['dni' => '11111111']);
        $jugadorDTO = $this->jugadorService->findByDni('11111111');

        $this->assertNotNull($jugadorDTO);
        $this->assertEquals($jugador->nombre, $jugadorDTO->nombre);
    }

    #[Test]
    public function puede_actualizar_un_jugador()
    {
        $jugador = Jugador::factory()->create();
        $jugador = $this->jugadorService->findById($jugador->id);
        $actualizado = $this->jugadorService->update($jugador, ['nombre' => 'Nombre Actualizado']);

        //$this->assertTrue($actualizado);
        $this->assertDatabaseHas('jugadores', ['id' => $jugador->id, 'nombre' => 'Nombre Actualizado']);
    }

    #[Test]
    public function puede_eliminar_un_jugador()
    {
        $jugador = Jugador::factory()->create();
        $eliminado = $this->jugadorService->delete($jugador->id);

        $this->assertTrue($eliminado);
        $this->assertSoftDeleted('jugadores', ['id' => $jugador->id]);
    }

    #[Test]
    public function puede_restaurar_un_jugador()
    {
        $jugador = Jugador::factory()->create();
        $this->jugadorService->delete($jugador->id);
        $restaurado = $this->jugadorService->restore($jugador->id);

        $this->assertTrue($restaurado);
        $this->assertDatabaseHas('jugadores', ['id' => $jugador->id]);
    }

    #[Test]
    public function puede_obtener_torneos_de_un_jugador()
    {
        $jugador = Jugador::factory()->create();
        $torneo = Torneo::factory()->create();
        $torneo->jugadores()->attach($jugador->id);

        $torneos = $this->jugadorService->getTorneos($jugador->id, false);

        $this->assertCount(1, $torneos);
        $this->assertEquals($torneo->id, $torneos[0]['id']);
    }

    #[Test]
    public function puede_obtener_partidas_de_un_jugador()
    {
        $jugador = Jugador::factory()->create();
        $partida = Partida::factory()->create(['jugador1_id' => $jugador->id]);

        $partidas = $this->jugadorService->getPartidas($jugador->id, 'todas');

        $this->assertCount(1, $partidas);
        $this->assertEquals($partida->id, $partidas[0]['id']);
    }
}
