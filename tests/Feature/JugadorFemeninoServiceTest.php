<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\JugadorFemeninoService;
use App\Repositories\JugadorRepository;
use App\Repositories\JugadorFemeninoRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use App\Models\JugadorFemenino;
use App\Models\Jugador;

class JugadorFemeninoServiceTest extends TestCase
{
    use RefreshDatabase;

    private JugadorFemeninoService $jugadorFemeninoService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jugadorFemeninoService = new JugadorFemeninoService(
            new JugadorRepository(),
            new JugadorFemeninoRepository()
        );
    }

    #[Test]
    public function puede_crear_un_jugador_femenino()
    {
        $data = [
            'nombre' => 'Ana López',
            'dni' => '12345678',
            'genero' => 'Femenino',
            'habilidad' => 85,
            'reaccion' => 90,
        ];

        $jugadorDTO = $this->jugadorFemeninoService->create($data);

        $this->assertDatabaseHas('jugadores', ['dni' => '12345678']);
        $this->assertDatabaseHas('jugadores_femeninos', ['id' => $jugadorDTO->id]);
    }

    #[Test]
    public function puede_obtener_un_jugador_femenino_por_id()
    {
        $jugador = Jugador::factory()->create(['genero' => 'Femenino']);
        $jugadorFemenino = JugadorFemenino::factory()->create(['id' => $jugador->id]);

        $jugadorDTO = $this->jugadorFemeninoService->findById($jugador->id);

        $this->assertEquals($jugador->nombre, $jugadorDTO->nombre);
        $this->assertEquals($jugador->dni, $jugadorDTO->dni);
        $this->assertEquals($jugadorFemenino->reaccion, $jugadorDTO->reaccion);
    }

    #[Test]
    public function puede_actualizar_un_jugador_femenino()
    {
        $jugador = Jugador::factory()->create(['genero' => 'Femenino']);
        JugadorFemenino::factory()->create(['id' => $jugador->id, 'reaccion' => 80]);

        $actualizado = $this->jugadorFemeninoService->update($jugador->id, ['reaccion' => 95]);

        $this->assertTrue($actualizado);
        $this->assertDatabaseHas('jugadores_femeninos', ['id' => $jugador->id, 'reaccion' => 95]);
    }

    #[Test]
    public function devuelve_error_si_el_jugador_no_existe()
    {
        $this->expectException(\Exception::class);
        $this->jugadorFemeninoService->findById(9999);
    }
}
