<?php

namespace App\Repositories;

use App\Interfaces\Repositories\IJugadorMasculinoRepository;
use App\Models\JugadorMasculino;
use Illuminate\Database\Eloquent\Collection;

class JugadorMasculinoRepository implements IJugadorMasculinoRepository
{
    public function getAll(): Collection
    {
        return JugadorMasculino::whereHas('jugador') //Solo incluye si el jugador existe y no está eliminado
            ->with('jugador') // Cargar la relación
            ->get();
    }

    public function findById(int $id): ?JugadorMasculino
    {
        return JugadorMasculino::find($id);
    }


    public function create(array $data): JugadorMasculino
    {
        return JugadorMasculino::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return JugadorMasculino::where('id', $id)->update($data);
    }
}
