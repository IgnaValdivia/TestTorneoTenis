<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Jugador;
use App\Models\JugadorFemenino;
use App\Repositories\JugadorFemeninoRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class JugadorFemeninoRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private JugadorFemeninoRepository $jugadorFemeninoRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->jugadorFemeninoRepository = new JugadorFemeninoRepository();
    }

    #[Test]
    public function puede_crear_un_jugador_femenino()
    {

        // Crear el jugador femenino
        $data = [
            'id' => Jugador::factory()->create(['genero' => 'Femenino'])->id,
            'reaccion' => 85
        ];

        $jugadorFemenino = $this->jugadorFemeninoRepository->create($data);

        $this->assertInstanceOf(JugadorFemenino::class, $jugadorFemenino);
        $this->assertDatabaseHas('jugadores_femeninos', $data);
    }

    #[Test]
    public function puede_obtener_todos_los_jugadores_femeninos()
    {
        Jugador::factory()->count(3)->create(['genero' => 'Femenino']);
        JugadorFemenino::factory()->count(3)->create();

        $jugadores = $this->jugadorFemeninoRepository->getAll();

        $this->assertCount(3, $jugadores);
    }

    #[Test]
    public function puede_buscar_un_jugador_femenino_por_id()
    {
        $jugadorBase = Jugador::factory()->create(['genero' => 'Femenino']);
        $jugadorFemenino = JugadorFemenino::factory()->create(['id' => $jugadorBase->id]);

        $encontrado = $this->jugadorFemeninoRepository->findById($jugadorFemenino->id);

        $this->assertNotNull($encontrado);
        $this->assertEquals($jugadorFemenino->id, $encontrado->id);
    }

    #[Test]
    public function puede_actualizar_un_jugador_femenino()
    {
        $jugadorBase = Jugador::factory()->create(['genero' => 'Femenino']);
        $jugadorFemenino = JugadorFemenino::factory()->create(['id' => $jugadorBase->id]);

        $nuevosDatos = ['reaccion' => 95];

        $actualizado = $this->jugadorFemeninoRepository->update($jugadorFemenino->id, $nuevosDatos);

        $this->assertTrue($actualizado);
        $this->assertDatabaseHas('jugadores_femeninos', [
            'id' => $jugadorFemenino->id,
            'reaccion' => 95
        ]);
    }
}
