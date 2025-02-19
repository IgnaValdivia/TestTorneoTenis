<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\JugadorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class JugadorController extends Controller
{

    private JugadorService $jugadorService;

    public function __construct(JugadorService $jugadorService)
    {
        $this->jugadorService = $jugadorService;
    }


    public function index(Request $request): JsonResponse
    {
        $jugadores = $this->jugadorService->getAll($request->query('genero'));

        if (empty($jugadores)) {
            return response()->json(['message' => 'No hay jugadores disponibles'], 200);
        }

        return response()->json($jugadores, 200);
    }


    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'dni' => 'required|string|unique:jugadores,dni',
            'genero' => 'required|in:Masculino,Femenino',
            'habilidad' => 'required|integer|min:0|max:100',
        ]);

        if ($data['genero'] === 'Masculino') {
            $extra = $request->validate([
                'fuerza' => 'required|integer|min:0|max:100',
                'velocidad' => 'required|integer|min:0|max:100',
            ]);
        } elseif ($data['genero'] === 'Femenino') {
            $extra = $request->validate([
                'reaccion' => 'required|integer|min:0|max:100',
            ]);
        }

        $data = array_merge($data, $extra ?? []);

        $jugador = $this->jugadorService->create($data['genero'], $data);

        return response()->json($jugador, 201);
    }


    public function show(int $id): JsonResponse
    {

        $jugador = $this->jugadorService->findById($id);

        if (!$jugador) {
            return response()->json(['message' => 'Jugador no encontrado'], 404);
        }

        return response()->json($jugador, 200);
    }


    public function showByDni(string $dni): JsonResponse
    {
        if (!preg_match('/^\d{7,8}$/', $dni)) {
            return response()->json(['error' => 'El DNI debe ser un número de 7 u 8 dígitos'], 400);
        }

        $jugador = $this->jugadorService->findByDni($dni);

        if (!$jugador) {
            return response()->json(['message' => 'Jugador no encontrado'], 404);
        }

        return response()->json($jugador, 200);
    }


    public function update(Request $request, int $id): JsonResponse
    {
        $jugador = $this->jugadorService->findById($id);

        if (!$jugador) {
            return response()->json(['message' => 'Jugador no encontrado'], 404);
        }

        // Si intentan cambiar el género, lanzar un error
        if ($request->has('genero')) {
            return response()->json([
                'message' => 'El campo género no puede ser modificado.'
            ], 400);
        }

        // Validación de datos generales
        $data = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'dni' => 'sometimes|string|unique:jugadores,dni,' . $id,
            'habilidad' => 'sometimes|integer|min:0|max:100',
        ]);

        if ($jugador->genero === 'Masculino') {
            $extra = $request->validate([
                'fuerza' => 'sometimes|integer|min:0|max:100',
                'velocidad' => 'sometimes|integer|min:0|max:100',
            ]);
        } elseif ($jugador->genero === 'Femenino') {
            $extra = $request->validate([
                'reaccion' => 'sometimes|integer|min:0|max:100',
            ]);
        }

        // Unimos los datos
        $data = array_merge($data, $extra);

        $actualizado = $this->jugadorService->update($jugador, $data);

        if (!$actualizado) {
            return response()->json(['message' => 'No se pudo actualizar el jugador'], 400);
        }

        return response()->json(['message' => 'Jugador actualizado correctamente'], 200);
    }


    public function destroy(string $id): JsonResponse
    {
        $resultado = $this->jugadorService->delete($id);

        if ($resultado === null) {
            return response()->json(['message' => 'Jugador no encontrado'], 404);
        }

        if ($resultado === false) {
            return response()->json(['message' => 'No se pudo eliminar el jugador'], 500);
        }

        return response()->json(['message' => 'Jugador eliminado correctamente'], 200);
    }


    public function torneos(int $id, Request $request): JsonResponse
    {
        try {
            $soloGanados = $request->query('ganados');
            $soloGanados = ($soloGanados === 'true') ? true : (($soloGanados === 'false') ? false : null);

            $torneos = $this->jugadorService->getTorneos($id, $soloGanados);

            // Si el jugador existe pero no tiene torneos, devolver mensaje diferente
            if (empty($torneos)) {
                return response()->json(['message' => 'El jugador no tiene torneos'], 200);
            }

            return response()->json($torneos, 200);
        } catch (NotFoundHttpException $e) {
            return response()->json(['message' => 'Jugador no encontrado'], 404);
        }
    }


    public function partidas(Request $request, int $id): JsonResponse
    {
        // Verificar si el jugador existe
        $jugador = $this->jugadorService->findById($id);
        if (!$jugador) {
            return response()->json(['message' => 'Jugador no encontrado'], 404);
        }

        // Obtener el filtro (por defecto "todas" si no se envía)
        $filtro = $request->query('filtro', null);

        // Obtener partidas del jugador
        $partidas = $this->jugadorService->getPartidas($id, $filtro);

        // Si el jugador existe pero no tiene partidas
        if (empty($partidas)) {
            return response()->json(['message' => 'El jugador no tiene partidas'], 200);
        }

        return response()->json($partidas, 200);
    }
}
