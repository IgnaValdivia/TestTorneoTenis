<?php

namespace App\DTOs;

use App\Models\Jugador;

class JugadorDTO
{
    public int $id;
    public string $nombre;
    public string $dni;
    public string $genero;
    public int $habilidad;
    public ?int $fuerza = null; // Solo para jugadores masculinos
    public ?int $velocidad = null; // Solo para jugadores masculinos
    public ?int $reaccion = null; // Solo para jugadores femeninos
    public string $created_at;
    public string $updated_at;

    public function __construct(Jugador $jugador)
    {
        $this->id = $jugador->id;
        $this->nombre = $jugador->nombre;
        $this->dni = $jugador->dni;
        $this->genero = $jugador->genero;
        $this->habilidad = $jugador->habilidad;
        $this->created_at = $jugador->created_at->toDateTimeString();
        $this->updated_at = $jugador->updated_at->toDateTimeString();

        // Si es masculino, agregar atributos de JugadorMasculino
        if ($jugador->genero === 'Masculino' && $jugador->jugadorMasculino) {
            $this->fuerza = $jugador->jugadorMasculino->fuerza;
            $this->velocidad = $jugador->jugadorMasculino->velocidad;
        }

        // Si es femenino, agregar atributos de JugadorFemenino
        if ($jugador->genero === 'Femenino' && $jugador->jugadorFemenino) {
            $this->reaccion = $jugador->jugadorFemenino->reaccion;
        }
    }

    public static function fromModel(Jugador $jugador): self
    {
        return new self($jugador);
    }
}
