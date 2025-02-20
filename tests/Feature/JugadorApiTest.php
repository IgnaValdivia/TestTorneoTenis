<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Jugador;
use App\Models\JugadorMasculino;
use App\Models\Torneo;
use App\Models\Partida;
use PHPUnit\Framework\Attributes\Test;

class JugadorApiTest extends TestCase
{
    use RefreshDatabase;

    //Obtener todos los jugadores 
    #[Test]
    public function test_obtener_todos_los_jugadores()
    {
        Jugador::factory()->count(3)->create();

        $response = $this->getJson(route('jugadores.index'));

        $response->assertStatus(200);
    }

    //Obtener todos los jugadores (sin jugadores creados)
    #[Test]
    public function test_obtener_todos_los_jugadores_sin_existencia()
    {
        $response = $this->getJson(route('jugadores.index'));

        $response->assertStatus(200)
            ->assertJson(['message' => 'No hay jugadores disponibles']);
    }

    //Obtener jugador por ID 
    #[Test]
    public function test_obtener_jugador_por_id()
    {
        $jugador = Jugador::factory()->create();

        $response = $this->getJson(route('jugadores.show', ['id' => $jugador->id]));

        $response->assertStatus(200)
            ->assertJson(['id' => $jugador->id]);
    }

    //Obtener jugador por ID (No existe)
    #[Test]
    public function test_obtener_jugador_por_id_no_existente()
    {
        $response = $this->getJson(route('jugadores.show', ['id' => 999]));

        $response->assertStatus(404)
            ->assertJson(['message' => 'Jugador no encontrado']);
    }

    //Obtener jugador por DNI
    #[Test]
    public function test_obtener_jugador_por_dni()
    {
        $jugador = Jugador::factory()->create();

        $response = $this->getJson(route('jugadores.showByDni', ['dni' => $jugador->dni]));

        $response->assertStatus(200)
            ->assertJson(['dni' => $jugador->dni]);
    }

    //Crear un jugador
    #[Test]
    public function test_crear_jugador()
    {
        $datos = [
            'nombre' => 'Juan Pérez',
            'dni' => '12345678',
            'genero' => 'Masculino',
            'habilidad' => 80,
            'fuerza' => 75,
            'velocidad' => 85
        ];

        $response = $this->postJson(route('jugadores.store'), $datos);

        $response->assertStatus(201)
            ->assertJson(['dni' => '12345678']);
    }

    //Crear jugador con datos inválidos
    #[Test]
    public function test_crear_jugador_con_datos_invalidos()
    {
        $datos = [
            'nombre' => '',
            'dni' => 'abc', // Inválido
            'genero' => 'Otro', // Inválido
            'habilidad' => 120 // Fuera de rango
        ];

        $response = $this->postJson(route('jugadores.store'), $datos);

        $response->assertStatus(422);
    }

    //Actualizar jugador
    #[Test]
    public function test_actualizar_jugador()
    {
        $jugador = JugadorMasculino::factory()->create();

        $nuevosDatos = [
            'nombre' => 'Nuevo Nombre',
            'habilidad' => 90,
            'fuerza' => 20
        ];

        $response = $this->putJson(route('jugadores.update', ['id' => $jugador->id]), $nuevosDatos);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Jugador actualizado correctamente']);
    }

    //Intentar cambiar el género (Error) 
    #[Test]
    public function test_no_se_puede_cambiar_genero()
    {
        $jugador = Jugador::factory()->create();

        $response = $this->putJson(route('jugadores.update', ['id' => $jugador->id]), ['genero' => 'Femenino']);

        $response->assertStatus(400)
            ->assertJson(['message' => 'El campo género no puede ser modificado.']);
    }

    //Eliminar jugador
    #[Test]
    public function test_eliminar_jugador()
    {
        $jugador = Jugador::factory()->create();

        $response = $this->deleteJson(route('jugadores.destroy', ['id' => $jugador->id]));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Jugador eliminado correctamente']);
    }

    //Obtener torneos de un jugador
    #[Test]
    public function test_obtener_torneos_de_un_jugador()
    {
        $jugador = Jugador::factory()->create();
        $torneo = Torneo::factory()->create();

        $torneo->jugadores()->attach($jugador->id);

        $response = $this->getJson(route('jugadores.torneos', ['id' => $jugador->id]));

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $torneo->id]);
    }

    //Obtener torneos cuando no tiene 
    #[Test]
    public function test_obtener_torneos_de_un_jugador_sin_torneos()
    {
        $jugador = Jugador::factory()->create();

        $response = $this->getJson(route('jugadores.torneos', ['id' => $jugador->id]));

        $response->assertStatus(200)
            ->assertJson(['message' => 'El jugador no tiene torneos']);
    }

    //Obtener partidas jugadas por un jugador 
    #[Test]
    public function test_obtener_partidas_de_un_jugador()
    {
        $jugador = Jugador::factory()->create();
        $torneo = Torneo::factory()->create();

        $partida = Partida::factory()->create([
            'torneo_id' => $torneo->id,
            'jugador1_id' => $jugador->id,
            'jugador2_id' => Jugador::factory()->create()->id,
            'ganador_id' => $jugador->id,
            'ronda' => 1
        ]);

        $response = $this->getJson(route('jugadores.partidas', ['id' => $jugador->id]));

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $partida->id]);
    }

    //Obtener partidas cuando el jugador no tiene
    #[Test]
    public function test_obtener_partidas_de_un_jugador_sin_partidas()
    {
        $jugador = Jugador::factory()->create();

        $response = $this->getJson(route('jugadores.partidas', ['id' => $jugador->id]));

        $response->assertStatus(200)
            ->assertJson(['message' => 'El jugador no tiene partidas']);
    }
}
