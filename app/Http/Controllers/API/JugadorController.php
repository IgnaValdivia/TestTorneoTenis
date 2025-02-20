<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\JugadorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="Jugadores",
 *     description="Endpoints relacionados con los jugadores"
 * )
 */
class JugadorController extends Controller
{

    private JugadorService $jugadorService;

    public function __construct(JugadorService $jugadorService)
    {
        $this->jugadorService = $jugadorService;
    }

    /**
     * Obtener todos los jugadores con filtro opcional por género.
     * 
     * @OA\Get(
     *     path="/api/jugadores",
     *     tags={"Jugadores"},
     *     summary="Listar todos los jugadores",
     *     description="Permite listar todos los jugadores, opcionalmente filtrando por género.",
     *     @OA\Parameter(
     *         name="genero",
     *         in="query",
     *         description="Filtrar por género (Masculino, Femenino, Todos)",
     *         required=false,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de jugadores o mensaje si no hay jugadores disponibles",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         required={"id", "nombre", "dni", "genero", "habilidad"},
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="nombre", type="string", example="Juan Pérez"),
     *                         @OA\Property(property="dni", type="string", example="12345678"),
     *                         @OA\Property(property="genero", type="string", enum={"Masculino", "Femenino"}),
     *                         @OA\Property(property="habilidad", type="integer", example=85),
     *                         @OA\Property(property="fuerza", type="integer", example=80, nullable=true),
     *                         @OA\Property(property="velocidad", type="integer", example=90, nullable=true),
     *                     ),
     *                 ),
     *                  @OA\Schema(
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         required={"id", "nombre", "dni", "genero", "habilidad"},
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="nombre", type="string", example="Juan Pérez"),
     *                         @OA\Property(property="dni", type="string", example="12345678"),
     *                         @OA\Property(property="genero", type="string", enum={"Masculino", "Femenino"}),
     *                         @OA\Property(property="habilidad", type="integer", example=85),
     *                         @OA\Property(property="reaccion", type="integer", example=80),
     *                     ),
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="message", type="string", example="No hay jugadores disponibles")
     *                 )
     *             }
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $jugadores = $this->jugadorService->getAll($request->query('genero'));

        if (empty($jugadores)) {
            return response()->json(['message' => 'No hay jugadores disponibles'], 200);
        }

        return response()->json($jugadores, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/jugadores",
     *     tags={"Jugadores"},
     *     summary="Crear un nuevo jugador",
     *     description="Registra un nuevo jugador con sus atributos.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre", "dni", "genero", "habilidad"},
     *             @OA\Property(property="nombre", type="string", example="Juan Pérez"),
     *             @OA\Property(property="dni", type="string", example="12345678"),
     *             @OA\Property(property="genero", type="string", enum={"Masculino", "Femenino"}, example="Masculino"),
     *             @OA\Property(property="habilidad", type="integer", example=85, minimum=0, maximum=100),
     *             @OA\Property(
     *                 property="fuerza",
     *                 type="integer",
     *                 example=80,
     *                 nullable=true,
     *                 description="Campo obligatorio si el género es 'Masculino'. Rango: 0 - 100."
     *             ),
     *             @OA\Property(
     *                 property="velocidad",
     *                 type="integer",
     *                 example=90,
     *                 nullable=true,
     *                 description="Campo obligatorio si el género es 'Masculino'. Rango: 0 - 100."
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Jugador creado exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="nombre", type="string", example="Juan Pérez"),
     *             @OA\Property(property="dni", type="string", example="12345678"),
     *             @OA\Property(property="genero", type="string", example="Masculino"),
     *             @OA\Property(property="habilidad", type="integer", example=85),
     *             @OA\Property(property="fuerza", type="integer", example=80),
     *             @OA\Property(property="velocidad", type="integer", example=90),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error en la validación",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Error en la validación"),
     *             @OA\Property(
     *                 property="detalles",
     *                 type="object",
     *                 @OA\Property(
     *                     property="dni",
     *                     type="array",
     *                     @OA\Items(type="string", example="El campo dni ya ha sido tomado.")
     *                 ),
     *                 @OA\Property(
     *                     property="genero",
     *                     type="array",
     *                     @OA\Items(type="string", example="El campo genero es obligatorio.")
     *                 )
     *             )
     *         )
     *     ),
     * )
     */
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

    /**
     * Obtener un jugador por ID.
     * 
     * @OA\Get(
     *     path="/api/jugadores/{id}",
     *     tags={"Jugadores"},
     *     summary="Obtener un jugador por ID",
     *     description="Busca un jugador en base a su ID único.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del jugador",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *          response=200, 
     *          description="Jugador encontrado",
     *          @OA\JsonContent(
     *             type="object",
     *             required={"id", "nombre", "dni", "genero", "habilidad"},
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="nombre", type="string", example="Juan Pérez"),
     *             @OA\Property(property="dni", type="string", example="12345678"),
     *             @OA\Property(property="genero", type="string", enum={"Masculino", "Femenino"}),
     *             @OA\Property(property="habilidad", type="integer", example=85),
     *             @OA\Property(property="fuerza", type="integer", example=80, nullable=true),
     *             @OA\Property(property="velocidad", type="integer", example=90, nullable=true),
     *           ),
     *      ),
     *     @OA\Response(response=404, description="Jugador no encontrado")
     * )
     */
    public function show(int $id): JsonResponse
    {

        $jugador = $this->jugadorService->findById($id);

        if (!$jugador) {
            return response()->json(['message' => 'Jugador no encontrado'], 404);
        }

        return response()->json($jugador, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/jugadores/{dni}",
     *     tags={"Jugadores"},
     *     summary="Obtener un jugador por DNI",
     *     description="Busca un jugador por su DNI único.",
     *     @OA\Parameter(
     *         name="dni",
     *         in="path",
     *         description="DNI del jugador (debe ser un número de 7 u 8 dígitos)",
     *         required=true,
     *         @OA\Schema(type="string", example="12345678")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Jugador encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="nombre", type="string", example="Juan Pérez"),
     *             @OA\Property(property="dni", type="string", example="12345678"),
     *             @OA\Property(property="genero", type="string", example="Masculino"),
     *             @OA\Property(property="habilidad", type="integer", example=80)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="DNI inválido",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="El DNI debe ser un número de 7 u 8 dígitos")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Jugador no encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Jugador no encontrado")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/api/jugadores/{id}",
     *     tags={"Jugadores"},
     *     summary="Actualizar un jugador",
     *     description="Actualiza los datos de un jugador. El campo 'genero' NO puede ser modificado.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del jugador",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={},
     *             @OA\Property(property="nombre", type="string", example="Juan Pérez"),
     *             @OA\Property(property="dni", type="string", example="12345678"),
     *             @OA\Property(property="habilidad", type="integer", example=85),
     *             @OA\Property(property="fuerza", type="integer", example=80, nullable=true, description="Campo obligatorio si el género es 'Masculino'."),
     *             @OA\Property(property="velocidad", type="integer", example=90, nullable=true, description="Campo obligatorio si el género es 'Masculino'."),
     *             @OA\Property(property="reaccion", type="integer", example=70, nullable=true, description="Campo obligatorio si el género es 'Femenino'.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Jugador actualizado correctamente",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Jugador actualizado correctamente"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Intento de modificar el género",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="El campo género no puede ser modificado."))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Jugador no encontrado",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Jugador no encontrado"))
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error en la validación",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Error en la validación"),
     *             @OA\Property(
     *                 property="detalles",
     *                 type="object",
     *                 @OA\Property(property="dni", type="array", @OA\Items(type="string", example="El campo dni ya ha sido tomado."))
     *             )
     *         )
     *     )
     * )
     */
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

    /**
     * Eliminar un jugador.
     * 
     * @OA\Delete(
     *     path="/api/jugadores/{id}",
     *     tags={"Jugadores"},
     *     summary="Eliminar un jugador",
     *     description="Realiza la eliminación lógica de un jugador por ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del jugador a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Jugador eliminado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Jugador eliminado correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Jugador no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Jugador no encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error en la eliminación del jugador",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No se pudo eliminar el jugador")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/jugadores/{id}/torneos",
     *     tags={"Jugadores"},
     *     summary="Obtener torneos en los que participa un jugador",
     *     description="Obtiene la lista de torneos en los que ha participado un jugador. Se puede filtrar por torneos ganados.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del jugador",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="ganados",
     *         in="query",
     *         description="Filtrar solo torneos ganados. Puede ser 'true' (solo ganados), 'false' (solo no ganados) o no enviar el parámetro para obtener todos.",
     *         required=false,
     *         @OA\Schema(type="string", enum={"true", "false"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de torneos del jugador o mensaje si no tiene torneos",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="nombre", type="string", example="Torneo Nacional"),
     *                         @OA\Property(property="fecha", type="string", format="date", example="2025-05-20"),
     *                         @OA\Property(property="tipo", type="string", example="Masculino"),
     *                         @OA\Property(property="ganador_id", type="integer", example=10, nullable=true)
     *                     )
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="message", type="string", example="El jugador no tiene torneos")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Jugador no encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Jugador no encontrado")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/jugadores/{id}/partidas",
     *     tags={"Jugadores"},
     *     summary="Obtener partidas en las que participa un jugador",
     *     description="Obtiene la lista de partidas en las que ha participado un jugador. Se puede filtrar por partidas ganadas o perdidas.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del jugador",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="filtro",
     *         in="query",
     *         description="Filtrar por partidas ganadas ('ganadas'), perdidas ('perdidas') o todas (sin enviar el parámetro)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"ganadas", "perdidas"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de partidas del jugador o mensaje si no tiene partidas",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="torneo_id", type="integer", example=5),
     *                         @OA\Property(property="jugador1_id", type="integer", example=10),
     *                         @OA\Property(property="jugador2_id", type="integer", example=20),
     *                         @OA\Property(property="ganador_id", type="integer", example=10, nullable=true),
     *                         @OA\Property(property="ronda", type="integer", example=1),
     *                         @OA\Property(property="fecha", type="string", format="date-time", example="2025-06-15T14:30:00Z")
     *                     )
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="message", type="string", example="El jugador no tiene partidas")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Jugador no encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Jugador no encontrado")
     *         )
     *     )
     * )
     */
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
