<?php

namespace App\Interfaces;

use App\DTOs\JugadorMasculinoDTO;

interface IJugadorMasculinoService
{
    public function getAll(): array;
    public function findById(int $id): ?JugadorMasculinoDTO;
    public function create($data): JugadorMasculinoDTO;
    public function update(int $id, array $data): bool;
}
