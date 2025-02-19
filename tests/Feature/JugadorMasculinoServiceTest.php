<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\JugadorMasculinoService;
use App\Repositories\JugadorRepository;
use App\Repositories\JugadorMasculinoRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\JugadorMasculino;
use App\Models\Jugador;
use PHPUnit\Framework\Attributes\Test;
use Exception;

class JugadorMasculinoServiceTest extends TestCase
{
    use RefreshDatabase;

    private JugadorMasculinoService $jugadorMasculinoService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jugadorMasculinoService = new JugadorMasculinoService(
            new JugadorRepository(),
            new JugadorMasculinoRepository()
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

        $jugadorDTO = $this->jugadorMasculinoService->create($data);

        $this->assertDatabaseHas('jugadores', ['dni' => '87654321']);
        $this->assertDatabaseHas('jugadores_masculinos', ['id' => $jugadorDTO->id]);
    }

    #[Test]
    public function puede_obtener_un_jugador_masculino_por_id()
    {
        $jugador = Jugador::factory()->create(['genero' => 'Masculino']);
        $jugadorMasculino = JugadorMasculino::factory()->create(['id' => $jugador->id]);

        $jugadorDTO = $this->jugadorMasculinoService->findById($jugador->id);

        $this->assertEquals($jugador->nombre, $jugadorDTO->nombre);
        $this->assertEquals($jugador->dni, $jugadorDTO->dni);
        $this->assertEquals($jugadorMasculino->fuerza, $jugadorDTO->fuerza);
        $this->assertEquals($jugadorMasculino->velocidad, $jugadorDTO->velocidad);
    }

    #[Test]
    public function puede_actualizar_un_jugador_masculino()
    {
        $jugador = Jugador::factory()->create(['genero' => 'Masculino']);
        JugadorMasculino::factory()->create(['id' => $jugador->id, 'fuerza' => 70, 'velocidad' => 75]);

        $actualizado = $this->jugadorMasculinoService->update($jugador->id, ['fuerza' => 95, 'velocidad' => 85]);

        $this->assertTrue($actualizado);
        $this->assertDatabaseHas('jugadores_masculinos', ['id' => $jugador->id, 'fuerza' => 95, 'velocidad' => 85]);
    }

    #[Test]
    public function devuelve_error_si_el_jugador_no_existe()
    {
        $this->expectException(Exception::class);
        $this->jugadorMasculinoService->findById(9999);
    }
}
