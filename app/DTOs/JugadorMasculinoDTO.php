<?php

namespace App\DTOs;

use App\Models\JugadorMasculino;

class JugadorMasculinoDTO
{
    public int $id;
    public string $nombre;
    public string $dni;
    public string $genero;
    public int $habilidad;
    public int $fuerza;
    public int $velocidad;
    //public string $created_at;
    //public string $updated_at;

    public function __construct(JugadorMasculino $jugadorMasculino)
    {
        $this->id = $jugadorMasculino->id;
        $this->nombre = $jugadorMasculino->jugador->nombre;
        $this->dni = $jugadorMasculino->jugador->dni;
        $this->genero = $jugadorMasculino->jugador->genero;
        $this->habilidad = $jugadorMasculino->jugador->habilidad;
        $this->fuerza = $jugadorMasculino->fuerza;
        $this->velocidad = $jugadorMasculino->velocidad;
        //$this->created_at = $jugadorMasculino->created_at->toDateTimeString();
        //$this->updated_at = $jugadorMasculino->updated_at->toDateTimeString();
    }

    public static function fromModel(JugadorMasculino $jugadorMasculino): self
    {
        return new self($jugadorMasculino);
    }
}
