<?php

namespace App\Interfaces;

use App\DTOs\JugadorFemeninoDTO;

interface IJugadorFemeninoService
{
    public function getAll(): array;
    public function findById(int $id): ?JugadorFemeninoDTO;
    public function create($data): JugadorFemeninoDTO;
    public function update(int $id, array $data): bool;
}
