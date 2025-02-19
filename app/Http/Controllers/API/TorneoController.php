<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Interfaces\ITorneoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class TorneoController extends Controller
{
    private ITorneoService $torneoService;

    public function __construct(ITorneoService $torneoService)
    {
        $this->torneoService = $torneoService;
    }


    public function index(): JsonResponse
    {
        $torneos = $this->torneoService->getAll();

        if (empty($torneos)) {
            return response()->json(['message' => 'No hay torneos disponibles'], 200);
        }

        return response()->json($torneos, 200);
    }


    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|in:Masculino,Femenino',
            'fecha' => 'required|date',
        ]);

        $torneo = $this->torneoService->create($data);

        return response()->json($torneo, 201);
    }


    public function show(int $id): JsonResponse
    {
        $torneo = $this->torneoService->findById($id);

        return $torneo
            ? response()->json($torneo, 200)
            : response()->json(['message' => 'Torneo no encontrado'], 404);
    }


    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'tipo' => 'sometimes|in:Masculino,Femenino',
            'estado' => 'sometimes|in:Finalizado,Pendiente',
            'ganador_id' => 'sometimes|integer',
            'fecha' => 'sometimes|date',
        ]);

        $actualizado = $this->torneoService->update($id, $data);
        return $actualizado
            ? response()->json(['message' => 'Torneo actualizado'], 200)
            : response()->json(['message' => 'Torneo no encontrado'], 404);
    }


    public function destroy(int $id): JsonResponse
    {
        $eliminado = $this->torneoService->delete($id);
        return $eliminado
            ? response()->json(['message' => 'Torneo eliminado'], 200)
            : response()->json(['message' => 'Torneo no encontrado'], 404);
    }


    public function partidas(int $id): JsonResponse
    {
        $torneo = $this->torneoService->findById($id);

        if (!$torneo) {
            return response()->json(['message' => 'Torneo con id ' . $id . ' no encontrado'], 404);
        }

        $partidas = $this->torneoService->getPartidas($id);

        if (empty($partidas)) {
            return response()->json(['message' => 'No hay partidas disponibles para el torneo con id: ' . $id], 200);
        }

        return response()->json($partidas, 200);
    }


    public function asignarJugadores(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'jugadores' => 'required|array',
        ]);

        $resultado = $this->torneoService->asignarJugadores($id, $data['jugadores']);

        return match (true) {
            $resultado === null => response()->json(['message' => 'Torneo no encontrado'], 404),

            is_array($resultado) && $resultado['error'] === 'no_existe' => response()->json([
                'message' => 'Uno o más jugadores no existen',
                'jugadores' => $resultado['jugadores']
            ], 400),

            is_array($resultado) && $resultado['error'] === 'genero_invalido' => response()->json([
                'message' => 'Uno o más jugadores no cumplen con el requisito de género del torneo',
                'jugadores' => $resultado['jugadores']
            ], 400),

            $resultado === true => response()->json(['message' => 'Jugadores asignados correctamente'], 200),

            default => response()->json(['message' => 'Error desconocido'], 500),
        };
    }


    public function comenzarTorneo(int $id): JsonResponse
    {
        $resultado = $this->torneoService->comenzarTorneo($id);

        if (isset($resultado['error'])) {
            $mensaje = $resultado['error'];

            return match ($mensaje) {
                'Torneo no encontrado' => response()->json(['message' => $mensaje], 404),
                'El torneo está finalizado' => response()->json(['message' => $mensaje], 409),
                default => response()->json(['message' => $mensaje], 422), // Casos de jugadores insuficientes
            };
        }

        return response()->json($resultado, 200);
    }


    public function estadoTorneo(int $id): JsonResponse
    {
        $estado = $this->torneoService->getEstado($id);

        return $estado
            ? response()->json(['estado' => $estado], 200)
            : response()->json(['message' => 'Torneo no encontrado'], 404);
    }


    public function partidasPorRonda(int $id, int $ronda): JsonResponse
    {
        $torneo = $this->torneoService->findById($id);

        if (!$torneo) {
            return response()->json(['message' => 'Torneo con id ' . $id . ' no encontrado'], 404);
        }

        $partidas = $this->torneoService->getPartidasPorRonda($id, $ronda);

        if (empty($partidas)) {
            return response()->json(['message' => 'No hay partidas disponibles para la ronda ' . $ronda . ' el torneo con id ' . $id], 200);
        }

        return response()->json($partidas, 200);
    }
}
