<?php

namespace App\Interfaces\Repositories;

use App\Models\Jugador;
use Illuminate\Database\Eloquent\Collection;

interface IJugadorRepository
{
    public function create(array $data): Jugador;
    public function update(int $id, array $data): bool;
    public function findById(int $id): ?Jugador;
    public function findByIds(array $ids): Collection;
    public function findByIdWithTrashed(int $id): ?Jugador;
    public function findByDni(string $dni): ?Jugador;
    public function delete(int $id): bool;
    public function restore(int $id): bool;
    public function getTorneos(int $id): Collection;
    public function getPartidas(int $id): Collection;
}
