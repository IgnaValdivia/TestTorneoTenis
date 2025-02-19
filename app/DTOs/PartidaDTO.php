<?php

namespace App\DTOs;

use App\Models\Partida;

class PartidaDTO
{
    public int $id;
    public int $torneo_id;
    public int $jugador1_id;
    public int $jugador2_id;
    public ?int $ganador_id;
    public int $ronda;

    public function __construct(Partida $partida)
    {
        $this->id = $partida->id;
        $this->torneo_id = $partida->torneo_id;
        $this->jugador1_id = $partida->jugador1_id;
        $this->jugador2_id = $partida->jugador2_id;
        $this->ganador_id = $partida->ganador_id;
        $this->ronda = $partida->ronda;
    }

    public static function fromModel(Partida $partida): self
    {
        return new self($partida);
    }
}
