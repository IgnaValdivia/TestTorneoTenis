<?php

namespace App\Interfaces;

use App\DTOs\JugadorDTO;
use App\DTOs\JugadorFemeninoDTO;
use App\DTOs\JugadorMasculinoDTO;
use App\Models\Jugador;
use Illuminate\Database\Eloquent\Collection;

interface IJugadorService
{
    public function create(string $genero, $data): JugadorMasculinoDTO | JugadorFemeninoDTO | null;
    public function findByDni(string $dni): ?JugadorDTO;
    public function findByIds(array $ids): ?Collection;
    public function update(JugadorDTO $jugador, array $data): bool;
    public function delete(int $id): ?bool;
    public function restore(int $id): ?bool;
    public function calcularPuntaje(Jugador $jugador): int;
    public function getTorneos(int $id,  ?bool $soloGanados): array;
    public function getPartidas(int $id, string $filtro): array;
}
