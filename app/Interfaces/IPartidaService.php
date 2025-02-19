<?php

namespace App\Interfaces;

use App\Models\Partida;

interface IPartidaService
{
    public function findById(int $id): ?Partida;
}
