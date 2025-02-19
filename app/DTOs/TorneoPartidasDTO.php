<?php

namespace App\DTOs;

use App\Models\Torneo;

class TorneoPartidasDTO
{
    public static function fromModel(Torneo $torneo): array
    {
        return [
            'nombre' => $torneo->nombre,
            'tipo' => $torneo->tipo,
            'estado' => $torneo->estado,
            'ganador' => $torneo->ganador->nombre,
            'fecha' => $torneo->fecha,
            'partidas' => $torneo->partidas->map(fn($partida) => [
                'ronda' => $partida->ronda,
                'jugador1' => $partida->jugador1->nombre,
                'jugador2' => $partida->jugador2->nombre,
                'ganador' => $partida->ganador->nombre,
            ])->toArray(),
        ];
    }
}
