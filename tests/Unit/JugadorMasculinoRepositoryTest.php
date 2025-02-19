<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Jugador;
use App\Models\JugadorMasculino;
use App\Repositories\JugadorMasculinoRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class JugadorMasculinoRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private JugadorMasculinoRepository $jugadorMasculinoRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->jugadorMasculinoRepository = new JugadorMasculinoRepository();
    }

    #[Test]
    public function puede_crear_un_jugador_masculino()
    {
        $data = [
            'id' => Jugador::factory()->create(['genero' => 'Masculino'])->id,
            'fuerza' => 90,
            'velocidad' => 75
        ];

        $jugadorMasculino = $this->jugadorMasculinoRepository->create($data);

        $this->assertInstanceOf(JugadorMasculino::class, $jugadorMasculino);
        $this->assertDatabaseHas('jugadores_masculinos', $data);
    }

    #[Test]
    public function puede_obtener_todos_los_jugadores_masculinos()
    {
        Jugador::factory()->count(3)->create(['genero' => 'Masculino']);
        JugadorMasculino::factory()->count(3)->create();

        $jugadores = $this->jugadorMasculinoRepository->getAll();

        $this->assertCount(3, $jugadores);
    }

    #[Test]
    public function puede_buscar_un_jugador_masculino_por_id()
    {
        $jugadorBase = Jugador::factory()->create(['genero' => 'Masculino']);
        $jugadorMasculino = JugadorMasculino::factory()->create(['id' => $jugadorBase->id]);

        $encontrado = $this->jugadorMasculinoRepository->findById($jugadorMasculino->id);

        $this->assertNotNull($encontrado);
        $this->assertEquals($jugadorMasculino->id, $encontrado->id);
    }

    #[Test]
    public function puede_actualizar_un_jugador_masculino()
    {
        $jugadorBase = Jugador::factory()->create(['genero' => 'Masculino']);
        $jugadorMasculino = JugadorMasculino::factory()->create(['id' => $jugadorBase->id]);

        $nuevosDatos = ['fuerza' => 95, 'velocidad' => 80];

        $actualizado = $this->jugadorMasculinoRepository->update($jugadorMasculino->id, $nuevosDatos);

        $this->assertTrue($actualizado);
        $this->assertDatabaseHas('jugadores_masculinos', [
            'id' => $jugadorMasculino->id,
            'fuerza' => 95,
            'velocidad' => 80
        ]);
    }
}
