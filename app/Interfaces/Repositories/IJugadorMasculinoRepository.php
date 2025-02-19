<?php

namespace App\Interfaces\Repositories;

use App\Models\JugadorMasculino;
use Illuminate\Database\Eloquent\Collection;

interface IJugadorMasculinoRepository
{
    public function getAll(): Collection;
    public function findById(int $id): ?JugadorMasculino;
    public function create(array $data): JugadorMasculino;
    public function update(int $id, array $data): bool;
}
