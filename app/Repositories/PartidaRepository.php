<?php

namespace App\Repositories;

use App\Interfaces\Repositories\IPartidaRepository;
use App\Models\Partida;

class PartidaRepository implements IPartidaRepository
{
    public function getAll()
    {
        return Partida::all();
    }

    public function findById(int $id): ?Partida
    {
        return Partida::find($id);
    }

    public function create(array $data): Partida
    {
        return Partida::create($data);
    }

    public function crearPartidas(array $partidas): void
    {
        foreach ($partidas as $partida) {
            Partida::create($partida);
        }
    }

    public function update(int $id, array $data): bool
    {
        return Partida::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return Partida::destroy($id);
    }

    public function deleteByTorneoId(int $torneoId): bool
    {
        return Partida::where('torneo_id', $torneoId)->delete();
    }
}
