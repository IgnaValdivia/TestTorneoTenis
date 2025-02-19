<?php

namespace App\DTOs;

use App\Models\Torneo;

class TorneoDTO
{
    public int $id;
    public string $nombre;
    public string $tipo;
    public string $estado;
    public ?int $ganador_id;
    public string $fecha;

    public function __construct(Torneo $torneo)
    {
        $this->id = $torneo->id;
        $this->nombre = $torneo->nombre;
        $this->tipo = $torneo->tipo;
        $this->estado = $torneo->estado;
        $this->ganador_id = $torneo->ganador_id;
        $this->fecha = $torneo->fecha;
    }

    public static function fromModel(Torneo $torneo): self
    {
        return new self($torneo);
    }
}
