<?php

namespace App\Repositories;

use App\Interfaces\Repositories\IJugadorFemeninoRepository;
use App\Models\JugadorFemenino;
use Illuminate\Database\Eloquent\Collection;

class JugadorFemeninoRepository implements IJugadorFemeninoRepository
{
    public function getAll(): Collection
    {
        return JugadorFemenino::whereHas('jugador')
            ->with('jugador')
            ->get();
    }

    public function findById(int $id): ?JugadorFemenino
    {
        return JugadorFemenino::find($id);
    }


    public function create(array $data): JugadorFemenino
    {
        return JugadorFemenino::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return JugadorFemenino::where('id', $id)->update($data);
    }
}
