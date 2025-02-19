<?php

namespace App\Services;

use App\DTOs\PartidaDTO;
use App\DTOs\TorneoDTO;
use App\DTOs\TorneoPartidasDTO;
use App\Interfaces\Repositories\IPartidaRepository;
use App\Interfaces\Repositories\ITorneoRepository;
use App\Models\Torneo;
use App\Models\Partida;
use App\Interfaces\ITorneoService;
use App\Interfaces\IJugadorService;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class TorneoService implements ITorneoService
{
    private ITorneoRepository $torneoRepository;
    private IPartidaRepository $partidaRepository;
    private IJugadorService $jugadorService;

    public function __construct(
        ITorneoRepository $torneoRepository,
        IPartidaRepository $partidaRepository,
        IJugadorService $jugadorService,
    ) {
        $this->torneoRepository = $torneoRepository;
        $this->partidaRepository = $partidaRepository;
        $this->jugadorService = $jugadorService;
    }

    public function getAll(): array
    {
        return $this->torneoRepository->getAll()->map(fn($torneo) => TorneoDTO::fromModel($torneo))
            ->toArray();
    }

    public function findById(int $id): ?TorneoDTO
    {
        $torneo = $this->torneoRepository->findById($id);

        if (!$torneo) {
            return null;
        }

        return TorneoDTO::fromModel($torneo);
    }

    public function findByIdConPartidas(int $id): ?Torneo
    {
        $torneo = $this->torneoRepository->findByIdConPartidas($id);

        if (!$torneo) {
            return null;
        }

        $torneo->partidas = $torneo->partidas->sortBy([
            ['ronda', 'asc'],
            ['id', 'asc']
        ]);

        return $torneo;
    }


    public function create(array $data): TorneoDTO
    {
        $data['estado'] = 'Pendiente';
        $torneo = $this->torneoRepository->create($data);
        return TorneoDTO::fromModel($torneo);
    }

    public function update(int $id, array $data): bool
    {
        $torneo = $this->torneoRepository->findById($id);

        if (!$torneo) {
            throw new Exception("Jugador no encontrado");
        }

        return $this->torneoRepository->update($id, [
            'nombre' => $data['nombre'] ?? $torneo->nombre,
            'tipo' => $data['tipo'] ?? $torneo->tipo,
            'fecha' => $data['fecha'] ?? $torneo->fecha,
        ]);
    }

    public function delete(int $id): bool
    {
        //Validar que exista el torneo

        $torneo = $this->torneoRepository->findById($id);

        if (!$torneo) {
            return false;
        }

        // Eliminar partidas del torneo antes de eliminar el torneo
        $this->partidaRepository->deleteByTorneoId($id);

        return $this->torneoRepository->delete($id);
    }

    public function restore(int $id): bool
    {
        $jugador = $this->torneoRepository->findByIdWithTrashed($id);

        if (!$jugador) {
            throw new Exception("Torneo no encontrado o no está eliminado.");
        }

        return $this->torneoRepository->restore($id);
    }

    public function determinarGanador(Partida $partida)
    {
        $intentos = 0;
        $maxIntentos = 3;

        do {
            $puntaje1 = $this->jugadorService->calcularPuntaje($partida->jugador1);
            $puntaje2 = $this->jugadorService->calcularPuntaje($partida->jugador2);

            if ($puntaje1 > $puntaje2) {
                $ganador = $partida->jugador1;
            } elseif ($puntaje2 > $puntaje1) {
                $ganador = $partida->jugador2;
            } else {
                $ganador = null; // No hay ganador aún
            }

            $intentos++;
        } while ($ganador === null && $intentos < $maxIntentos);

        // Si después de los intentos sigue en empate, se elige al azar (factor suerte 2)
        if ($ganador === null) {
            $ganador = rand(0, 1) ? $partida->jugador1 : $partida->jugador2;
        }

        $this->partidaRepository->update($partida->id, ['puntaje1' => $puntaje1,  'puntaje2' => $puntaje2, 'ganador_id' => $ganador->id]);

        return $ganador;
    }

    public function actualizarGanador(int $torneoId, int $ganadorId): bool
    {
        return $this->torneoRepository->update($torneoId, ['ganador_id' => $ganadorId, 'estado' => 'Finalizado']);
    }

    public function getPartidas(int $id): array
    {
        return $this->torneoRepository->getPartidas($id)->map(fn($partida) => PartidaDTO::fromModel($partida))
            ->toArray();;
    }

    public function asignarJugadores(int $id, array $jugadores): array|string|bool|null
    {
        $torneo = $this->torneoRepository->findById($id);

        if (!$torneo) {
            return null;
        }

        // Obtener los jugadores existentes en la BD
        $jugadoresValidos = $this->jugadorService->findByIds($jugadores);

        // Obtener los jugadores que no existen comparando los IDs enviados con los encontrados en la BD
        $jugadoresNoExistentes = array_diff($jugadores, $jugadoresValidos->pluck('id')->toArray());

        if (!empty($jugadoresNoExistentes)) {
            return ['error' => 'no_existe', 'jugadores' => array_values($jugadoresNoExistentes)];
        }

        // Validar que todos los jugadores sean del mismo género que el torneo
        $jugadoresGeneroIncorrecto = [];

        foreach ($jugadoresValidos as $jugador) {
            if ($jugador->genero !== $torneo->tipo) {
                $jugadoresGeneroIncorrecto[] = $jugador->id;
            }
        }

        if (!empty($jugadoresGeneroIncorrecto)) {
            return ['error' => 'genero_invalido', 'jugadores' => $jugadoresGeneroIncorrecto];
        }

        $this->torneoRepository->asignarJugadores($id, $jugadores);
        return true; // Asignación exitosa
    }

    public function obtenerJugadores(int $id): ?Collection
    {
        return $this->torneoRepository->obtenerJugadores($id);
    }

    public function comenzarTorneo(int $id): ?array
    {
        //Obtener el torneo
        $torneo = $this->torneoRepository->findById($id);

        if (!$torneo) {
            return ['error' => 'Torneo no encontrado'];
        }

        //Verificar que no este finalizado
        if ($torneo->estado === 'Finalizado') {
            return ['error' => 'El torneo ya está finalizado'];
        }

        //Obtener jugadores asociados al torneo
        $jugadores = $this->obtenerJugadores($id);

        if ($jugadores->isEmpty()) {
            return ['error' => 'No hay jugadores asignados al torneo'];
        }

        //Verificar si la cantidad de jugadores es potencia de 2
        $cantidadJugadores = count($jugadores);

        if (($cantidadJugadores & ($cantidadJugadores - 1)) !== 0) {
            //Calcular la próxima potencia de 2
            $proximaPotencia = pow(2, ceil(log($cantidadJugadores, 2)));
            $faltantes = $proximaPotencia - $cantidadJugadores;

            return ['error' => "El número de jugadores debe ser potencia de 2. Faltan $faltantes jugadores para completar."];
        }


        //Iniciar el torneo
        $ronda = 1;

        while (count($jugadores) > 1) {
            $ganadores = collect();

            //Mezclar jugadores aleatoriamente
            $jugadores = $jugadores->shuffle();

            for ($i = 0; $i < count($jugadores); $i += 2) {
                $jugador1 = $jugadores[$i];
                $jugador2 = $jugadores[$i + 1];

                // Crear la partida en el repositorio
                $partida = $this->partidaRepository->create([
                    'torneo_id' => $torneo->id,
                    'ronda' => $ronda,
                    'jugador1_id' => $jugador1->id,
                    'jugador2_id' => $jugador2->id,
                ]);

                //Determinar el ganador de la partida
                $ganador = $this->determinarGanador($partida);

                //Guardar al ganador para la siguiente ronda
                $ganadores->push($ganador);
            }

            //Actualizar la lista de jugadores con los ganadores
            $jugadores = $ganadores;
            $ronda++;
        }

        //Asignar el ganador al torneo
        $this->actualizarGanador($torneo->id, $jugadores->first()->id);

        // Obtener el torneo con sus partidas
        $torneoActualizado = $this->findByIdConPartidas($torneo->id);

        return [
            'message' => 'Torneo comenzado exitosamente',
            'torneo' => TorneoPartidasDTO::fromModel($torneoActualizado),
        ];
    }

    public function getEstado(int $id): ?string
    {
        return $this->torneoRepository->getEstado($id);
    }

    public function getPartidasPorRonda(int $id, int $ronda): array
    {
        return $this->torneoRepository->getPartidasPorRonda($id, $ronda)->map(fn($partida) => PartidaDTO::fromModel($partida))
            ->toArray();
    }
}
