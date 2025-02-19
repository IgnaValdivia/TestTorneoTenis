<?php

namespace App\Services;

use App\DTOs\JugadorMasculinoDTO;
use App\Interfaces\IJugadorMasculinoService;
use App\Interfaces\Repositories\IJugadorMasculinoRepository;
use App\Interfaces\Repositories\IJugadorRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class JugadorMasculinoService implements IJugadorMasculinoService
{
    private IJugadorRepository $jugadorRepository;
    private IJugadorMasculinoRepository $jugadorMasculinoRepository;

    public function __construct(IJugadorRepository $jugadorRepository, IJugadorMasculinoRepository $jugadorMasculinoRepository)
    {
        $this->jugadorRepository = $jugadorRepository;
        $this->jugadorMasculinoRepository = $jugadorMasculinoRepository;
    }

    public function getAll(): array
    {
        return $this->jugadorMasculinoRepository->getAll()
            ->map(fn($jugadorMasculino) => JugadorMasculinoDTO::fromModel($jugadorMasculino))
            ->toArray();
    }

    public function findById(int $id): ?JugadorMasculinoDTO
    {
        $jugadorMasculino = $this->jugadorMasculinoRepository->findById($id);

        if (!$jugadorMasculino) {
            throw new Exception("Jugador no encontrado");
        }

        return JugadorMasculinoDTO::fromModel($jugadorMasculino);
    }

    public function create($data): JugadorMasculinoDTO
    {
        return DB::transaction(function () use ($data) {
            // Crear jugador base en `jugadores`
            $jugador = $this->jugadorRepository->create([
                'nombre' => $data['nombre'],
                'dni' => $data['dni'],
                'genero' => 'Masculino',
                'habilidad' => $data['habilidad'],
            ]);


            $jugadorMasculino = $this->jugadorMasculinoRepository->create([
                'id' => $jugador->id,
                'velocidad' => $data['velocidad'],
                'fuerza' => $data['fuerza'],
            ]);

            // Retornar un DTO con los datos completos
            return JugadorMasculinoDTO::fromModel($jugadorMasculino);
        });
    }

    public function update(int $id, array $data): bool
    {
        return $this->jugadorMasculinoRepository->update($id, [
            'fuerza' => $data['fuerza'],
            'velocidad' => $data['velocidad'],
        ]);
    }
}
