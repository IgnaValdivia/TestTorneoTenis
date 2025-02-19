<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\PartidaService;
use Illuminate\Http\JsonResponse;


class PartidaController extends Controller
{
    private PartidaService $partidaService;

    public function __construct(PartidaService $partidaService)
    {
        $this->partidaService = $partidaService;
    }



    public function show(string $id): JsonResponse
    {
        $partida = $this->partidaService->findById($id);

        if (!$partida) {
            return response()->json(['error' => 'Partida no encontrada'], 404);
        }

        return response()->json($partida);
    }
}
