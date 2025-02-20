<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Torneo;
use App\Models\Jugador;
use App\Models\JugadorMasculino;
use App\Models\Partida;
use PHPUnit\Framework\Attributes\Test;

class TorneoApiTest extends TestCase
{
    use RefreshDatabase;

    //Listar todos los torneos
    #[test]
    public function puede_listar_torneos()
    {
        Torneo::factory()->count(3)->create();

        $response = $this->getJson(route('torneos.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['id', 'nombre', 'tipo', 'fecha', 'estado', 'ganador_id']
            ]);
    }

    //Listar todos los torneos (si no hay torneos)
    #[test]
    public function devuelve_mensaje_si_no_hay_torneos()
    {
        $response = $this->getJson(route('torneos.index'));

        $response->assertStatus(200)
            ->assertJson(['message' => 'No hay torneos disponibles']);
    }

    //Crear un torneo
    #[test]
    public function puede_crear_un_torneo()
    {
        $data = [
            'nombre' => 'Torneo Test',
            'tipo' => 'Masculino',
            'fecha' => now()->toDateString()
        ];

        $response = $this->postJson(route('torneos.store'), $data);

        $response->assertStatus(201)
            ->assertJsonFragment(['nombre' => 'Torneo Test']);
    }

    //Crear un torneo (con datos invalidos)
    #[test]
    public function valida_datos_al_crear_torneo()
    {
        $response = $this->postJson(route('torneos.store'), []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'error',
                'detalles' => [
                    'nombre',
                    'tipo',
                    'fecha'
                ]
            ]);
    }

    //Obtener un torneo por ID
    #[test]
    public function puede_obtener_un_torneo_por_id()
    {
        $torneo = Torneo::factory()->create();

        $response = $this->getJson(route('torneos.show', ['id' => $torneo->id]));

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $torneo->id]);
    }

    //Obtener un torneo por ID (No existe)
    #[test]
    public function devuelve_error_si_torneo_no_existe()
    {
        $response = $this->getJson(route('torneos.show', '999'));

        $response->assertStatus(404)
            ->assertJson(['message' => 'Torneo no encontrado']);
    }

    //Actualizar un torneo
    #[test]
    public function puede_actualizar_un_torneo()
    {
        $torneo = Torneo::factory()->create();

        $response = $this->putJson(route('torneos.update', ['id' => $torneo->id]), [
            'nombre' => 'Nuevo Nombre'
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Torneo actualizado']);

        $this->assertDatabaseHas('torneos', ['id' => $torneo->id, 'nombre' => 'Nuevo Nombre']);
    }

    //Actualizar un torneo (No existe)
    #[test]
    public function devuelve_error_al_actualizar_torneo_no_existente()
    {
        $response = $this->putJson(route('torneos.update', '999'), [
            'nombre' => 'Nuevo Nombre'
        ]);

        $response->assertStatus(404)
            ->assertJson(['message' => 'Torneo no encontrado']);
    }

    //Eliminar un torneo
    #[test]
    public function puede_eliminar_un_torneo()
    {
        $torneo = Torneo::factory()->create();

        $response = $this->deleteJson(route('torneos.destroy', ['id' => $torneo->id]));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Torneo eliminado']);

        $this->assertSoftDeleted('torneos', ['id' => $torneo->id]);
    }

    //Eliminar un torneo (No existe)
    #[test]
    public function devuelve_error_si_torneo_no_existe_al_eliminar()
    {
        $response = $this->deleteJson(route('torneos.destroy', ['id' => '999']));

        $response->assertStatus(404)
            ->assertJson(['message' => 'Torneo no encontrado']);
    }

    //Obtener partidas de un torneo
    #[test]
    public function puede_obtener_partidas_de_un_torneo()
    {
        $torneo = Torneo::factory()->create();

        $response = $this->getJson(route('torneos.partidas', ['id' => $torneo->id]));

        $response->assertStatus(200);
    }

    //Obtener partidas de un torneo (Sin partidas)
    #[test]
    public function devuelve_mensaje_si_no_hay_partidas()
    {
        $torneo = Torneo::factory()->create();

        $response = $this->getJson(route('torneos.partidas', ['id' => $torneo->id]));

        $response->assertStatus(200)
            ->assertJson(['message' => "No hay partidas disponibles para el torneo con id: {$torneo->id}"]);
    }

    //Asignar jugadores a torneo
    #[test]
    public function puede_asignar_jugadores_a_un_torneo()
    {
        $torneo = Torneo::factory()->create();
        $jugadores = Jugador::factory()->count(4)->create(['genero' => $torneo->tipo])->pluck('id')->toArray();

        $response = $this->postJson(route('torneos.asignarJugadores', $torneo->id), [
            'jugadores' => $jugadores
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Jugadores asignados correctamente']);
    }

    //Asignar jugadores a torneo que no existen
    #[test]
    public function devuelve_error_si_no_existen_jugadores_al_asignar()
    {
        $torneo = Torneo::factory()->create();

        $response = $this->postJson(route('torneos.asignarJugadores', $torneo->id), [
            'jugadores' => [999, 1000]
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Uno o más jugadores no existen',
                'jugadores' => [999, 1000]
            ]);
    }

    //Comenzar torneo
    #[test]
    public function puede_comenzar_un_torneo()
    {
        $torneo = Torneo::factory()->create(['tipo' => 'Masculino']);
        $jugadores = JugadorMasculino::factory()->count(4)->create()->pluck('id')->toArray();
        $torneo->jugadores()->attach($jugadores);

        $response = $this->getJson(route('torneos.comenzar', ['id' => $torneo->id]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'torneo',
            ]);
    }

    //Comenzar torneo (No existe)
    #[test]
    public function devuelve_error_si_torneo_no_existe_al_comenzar()
    {
        $response = $this->getJson(route('torneos.comenzar', '999'));

        $response->assertStatus(404)
            ->assertJson(['message' => 'Torneo no encontrado']);
    }

    //Comenzar torneo (Ya finalizado)
    #[test]
    public function devuelve_error_si_torneo_ya_finalizo()
    {
        $torneo = Torneo::factory()->create(['estado' => 'Finalizado']);

        $response = $this->getJson(route('torneos.comenzar', ['id' => $torneo->id]));

        $response->assertStatus(422)
            ->assertJson(['message' => 'El torneo ya está finalizado']);
    }

    //Comenzar torneo (Sin jugadores)
    #[test]
    public function devuelve_error_si_no_hay_jugadores_para_comenzar()
    {
        $torneo = Torneo::factory()->create();

        $response = $this->getJson(route('torneos.comenzar', ['id' => $torneo->id]));

        $response->assertStatus(422)
            ->assertJson(['message' => 'No hay jugadores asignados al torneo']);
    }

    //Obtener partidas por ronda
    #[test]
    public function puede_obtener_partidas_por_ronda_de_un_torneo()
    {
        $torneo = Torneo::factory()->create();

        $jugador1 = Jugador::factory()->create(['genero' => $torneo->tipo]);
        $jugador2 = Jugador::factory()->create(['genero' => $torneo->tipo]);

        $partida = Partida::factory()->create([
            'torneo_id' => $torneo->id,
            'jugador1_id' => $jugador1->id,
            'jugador2_id' => $jugador2->id,
            'ronda' => 1
        ]);

        $response = $this->getJson(route('torneos.partidasPorRonda', [
            'id' => $torneo->id,
            'ronda' => 1
        ]));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $partida->id,
                'torneo_id' => $torneo->id,
                'jugador1_id' => $jugador1->id,
                'jugador2_id' => $jugador2->id,
                'ronda' => 1
            ]);
    }

    //Obtener partidas por ronda de un torneo inexistente
    #[test]
    public function torneo_no_existe_al_buscar_partidas_por_ronda()
    {
        $response = $this->getJson(route('torneos.partidasPorRonda', [
            'id' => 999,
            'ronda' => 1
        ]));

        $response->assertStatus(404)
            ->assertJson(['message' => 'Torneo con id 999 no encontrado']);
    }

    //Obtener partidas por ronda de un torneo que no tiene
    #[test]
    public function torneo_si_no_hay_partidas_para_la_ronda()
    {
        $torneo = Torneo::factory()->create();

        $response = $this->getJson(route('torneos.partidasPorRonda', [
            'id' => $torneo->id,
            'ronda' => 2
        ]));

        $response->assertStatus(200)
            ->assertJson(['message' => "No hay partidas disponibles para la ronda 2 el torneo con id {$torneo->id}"]);
    }

    //Obtener el estado de un torneo
    #[test]
    public function puede_obtener_el_estado_de_un_torneo()
    {
        $torneo = Torneo::factory()->create();

        $response = $this->getJson(route('torneos.estado', ['id' => $torneo->id]));

        $response->assertStatus(200)
            ->assertJson(['estado' => 'Pendiente']);
    }

    //Obtener el estado de un torneo que no existe
    #[test]
    public function torneo_no_existe_al_consultar_estado()
    {
        $response = $this->getJson(route('torneos.estado', ['id' => 999]));

        $response->assertStatus(404)
            ->assertJson(['message' => 'Torneo no encontrado']);
    }
}
