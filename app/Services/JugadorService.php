<?php

namespace App\Services;

use App\DTOs\JugadorDTO;
use App\DTOs\JugadorFemeninoDTO;
use App\DTOs\JugadorMasculinoDTO;
use App\Interfaces\IJugadorFemeninoService;
use App\Interfaces\IJugadorMasculinoService;
use App\Interfaces\IJugadorService;
use App\Interfaces\Repositories\IJugadorRepository;
use App\Models\Jugador;
use App\Models\JugadorFemenino;
use App\Models\JugadorMasculino;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class JugadorService implements IJugadorService
{
    private IJugadorRepository $jugadorRepository;
    private IJugadorMasculinoService $jugadorMasculinoService;
    private IJugadorFemeninoService $jugadorFemeninoService;

    public function __construct(IJugadorRepository $jugadorRepository, IJugadorMasculinoService $jugadorMasculinoService, IJugadorFemeninoService $jugadorFemeninoService)
    {
        $this->jugadorRepository = $jugadorRepository;
        $this->jugadorMasculinoService = $jugadorMasculinoService;
        $this->jugadorFemeninoService = $jugadorFemeninoService;
    }

    public function getAll(?string $genero = null): array
    {
        return match (strtolower($genero)) {
            'masculino' => $this->jugadorMasculinoService->getAll(),
            'femenino' => $this->jugadorFemeninoService->getAll(),
            default => array_merge(
                $this->jugadorMasculinoService->getAll(),
                $this->jugadorFemeninoService->getAll()
            ),
        };
    }

    public function findById(int $id): ?JugadorDTO
    {
        $jugador = $this->jugadorRepository->findById($id);

        if (!$jugador) {
            return null;
        }

        return JugadorDTO::fromModel($jugador);
    }

    public function findByIds(array $ids): ?Collection
    {
        return $this->jugadorRepository->findByIds($ids);
    }

    public function findByDni(string $dni): ?JugadorDTO
    {
        $jugador = $this->jugadorRepository->findByDni($dni);

        if (!$jugador) {
            return null;
        }

        return JugadorDTO::fromModel($jugador);
    }

    public function create(string $genero, $data): JugadorMasculinoDTO | JugadorFemeninoDTO | null
    {
        try {
            return match (strtolower($genero)) {
                'masculino' => $this->jugadorMasculinoService->create($data),
                'femenino' => $this->jugadorFemeninoService->create($data),
                default => throw new InvalidArgumentException("Género inválido: $genero"),
            };
        } catch (Exception $e) {
            Log::error("Error al crear jugador: " . $e->getMessage());

            return null;
        }
    }

    public function update(JugadorDTO $jugador, array $data): bool
    {
        return DB::transaction(function () use ($jugador, $data) {

            // Actualizar los datos en `jugadores`
            $this->jugadorRepository->update($jugador->id, [
                'nombre' => $data['nombre'] ?? $jugador->nombre,
                'dni' => $data['dni'] ?? $jugador->dni,
                'habilidad' => $data['habilidad'] ?? $jugador->habilidad,
            ]);


            if ($jugador->genero === 'Masculino') {
                // Actualizar los datos en `jugadores_masculinos`
                return $this->jugadorMasculinoService->update($jugador->id, [
                    'fuerza' => $data['fuerza'] ?? $jugador->fuerza,
                    'velocidad' => $data['velocidad'] ?? $jugador->velocidad,
                ]);
            } else {
                // Actualizar los datos en `jugadores_femenino`
                return $this->jugadorFemeninoService->update($jugador->id, [
                    'reaccion' => $data['reaccion'] ?? $jugador->reaccion,
                ]);
            }
        });
    }

    public function delete(int $id): ?bool
    {
        $jugador = $this->jugadorRepository->findById($id);

        if (!$jugador) {
            return null;
        }

        return $this->jugadorRepository->delete($id) ?: false;
    }

    public function restore(int $id): ?bool
    {
        $jugador = $this->jugadorRepository->findByIdWithTrashed($id);

        if (!$jugador) {
            return null;
        }

        return $this->jugadorRepository->restore($id);
    }

    public function calcularPuntaje(Jugador $jugador): int
    {
        return match ($jugador->genero) {
            'Masculino' => $jugador->habilidad + $jugador->jugadorMasculino->fuerza + $jugador->jugadorMasculino->velocidad + rand(0, 10),
            'Femenino' => $jugador->habilidad + $jugador->jugadorFemenino->reaccion + rand(0, 10)
        };
    }

    public function getTorneos(int $id, ?bool $soloGanados): array
    {
        // Verificar si el jugador existe
        $jugador = $this->jugadorRepository->findById($id);
        if (!$jugador) {
            throw new NotFoundHttpException("Jugador no encontrado");
        }

        // Obtener torneos del jugador
        $torneos = $this->jugadorRepository->getTorneos($id);

        // Si no tiene torneos, devolver un array vacío sin error
        if ($torneos->isEmpty()) {
            return [];
        }

        // Si es true, solo los que ganó | Si es false, solo los que no ganó
        if (!is_null($soloGanados)) {
            $torneos = $torneos->filter(fn($torneo) => ($torneo->ganador_id === $id) === $soloGanados);
        }

        return $torneos->map(fn($torneo) => [
            'id' => $torneo->id,
            'nombre' => $torneo->nombre,
            'fecha' => $torneo->fecha,
            'tipo' => $torneo->tipo,
            'ganador_id' => $torneo->ganador_id
        ])->toArray();
    }

    public function getPartidas(int $id, ?string $filtro): array
    {
        $partidas = $this->jugadorRepository->getPartidas($id);

        // Aplica el filtro según la opción enviada
        return $partidas->filter(function ($partida) use ($id, $filtro) {
            return match ($filtro) {
                'ganadas' => $partida->ganador_id === $id,
                'perdidas' => $partida->ganador_id !== null && $partida->ganador_id !== $id,
                default => true, // Si es null, devuelve todas las partidas
            };
        })->map(fn($partida) => [
            'id' => $partida->id,
            'torneo_id' => $partida->torneo_id,
            'jugador1_id' => $partida->jugador1_id,
            'jugador2_id' => $partida->jugador2_id,
            'ganador_id' => $partida->ganador_id,
            'ronda' => $partida->ronda,
            'fecha' => $partida->created_at,
        ])->toArray();
    }
}
