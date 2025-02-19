<?php

namespace App\DTOs;

use App\Models\JugadorFemenino;

class JugadorFemeninoDTO
{
    public int $id;
    public string $nombre;
    public string $dni;
    public string $genero;
    public int $habilidad;
    public int $reaccion;
    //public string $created_at;
    //public string $updated_at;

    public function __construct(JugadorFemenino $jugadorFemenino)
    {
        $this->id = $jugadorFemenino->id;
        $this->nombre = $jugadorFemenino->jugador->nombre;
        $this->dni = $jugadorFemenino->jugador->dni;
        $this->genero = $jugadorFemenino->jugador->genero;
        $this->habilidad = $jugadorFemenino->jugador->habilidad;
        $this->reaccion = $jugadorFemenino->reaccion;
        //$this->created_at = $jugadorFemenino->created_at->toDateTimeString();
        //$this->updated_at = $jugadorFemenino->updated_at->toDateTimeString();
    }

    public static function fromModel(JugadorFemenino $jugadorFemenino): self
    {
        return new self($jugadorFemenino);
    }
}
