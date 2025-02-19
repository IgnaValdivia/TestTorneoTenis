<?php

namespace App\Interfaces\Repositories;

use App\Models\Partida;

interface IPartidaRepository
{
    public function getAll();
    public function findById(int $id): ?Partida;
    public function create(array $data): Partida;
    public function crearPartidas(array $partidas): void;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function deleteByTorneoId(int $torneoId): bool;
}
