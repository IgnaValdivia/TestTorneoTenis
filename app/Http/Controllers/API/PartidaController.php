<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\PartidaService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Partidas",
 *     description="Endpoints para gestionar partidas"
 * )
 */
class PartidaController extends Controller
{
    private PartidaService $partidaService;

    public function __construct(PartidaService $partidaService)
    {
        $this->partidaService = $partidaService;
    }


    /**
     * @OA\Get(
     *     path="/api/partidas/{id}",
     *     summary="Obtener detalles de una partida",
     *     description="Devuelve información de una partida específica por ID",
     *     tags={"Partidas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la partida",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de la partida",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="torneo_id", type="integer", example=3),
     *             @OA\Property(property="jugador1_id", type="integer", example=10),
     *             @OA\Property(property="jugador2_id", type="integer", example=20),
     *             @OA\Property(property="ganador_id", type="integer", example=10),
     *             @OA\Property(property="ronda", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Partida no encontrada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Partida no encontrada")
     *         )
     *     )
     * )
     */
    public function show(string $id): JsonResponse
    {
        $partida = $this->partidaService->findById($id);

        if (!$partida) {
            return response()->json(['error' => 'Partida no encontrada'], 404);
        }

        return response()->json($partida);
    }
}
