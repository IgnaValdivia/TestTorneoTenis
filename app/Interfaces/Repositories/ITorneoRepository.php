<?php

namespace App\Interfaces\Repositories;

use App\Models\Torneo;
use Illuminate\Database\Eloquent\Collection;

interface ITorneoRepository
{
    public function getAll();
    public function findById(int $id): ?Torneo;
    public function findByIdConPartidas(int $id): ?Torneo;
    public function create(array $data): Torneo;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function restore(int $id): bool;
    public function findByIdWithTrashed(int $id): ?Torneo;
    public function getPartidas(int $torneoId): Collection;
    public function obtenerJugadores(int $torneoId): Collection;
    public function asignarJugadores(int $torneoId, array $jugadores): void;
    public function getEstado(int $torneoId): ?string;
    public function getPartidasPorRonda(int $torneoId, int $ronda): Collection;
}
