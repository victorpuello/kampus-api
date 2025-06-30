<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAnioRequest;
use App\Http\Requests\UpdateAnioRequest;
use App\Http\Resources\AnioResource;
use App\Models\Anio;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Años",
 *     description="Operaciones relacionadas con la gestión de años académicos"
 * )
 */
class AnioController extends Controller
{
    /**
     * Constructor del controlador.
     * Aplica políticas de autorización a los recursos de año.
     */
    public function __construct()
    {
        parent::__construct();
        $this->authorizeResource(Anio::class, 'anio');
    }

    /**
     * @OA\Get(
     *     path="/v1/anios",
     *     summary="Obtiene una lista paginada de años académicos",
     *     tags={"Años"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de años por página",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Término de búsqueda para filtrar años por nombre",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="institucion_id",
     *         in="query",
     *         description="ID de la institución para filtrar años",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="estado",
     *         in="query",
     *         description="Estado del año (activo, inactivo)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"activo", "inactivo"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de años obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/AnioResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado",
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Anio::query()
            ->with('institucion')
            ->when($request->search, function ($query, $search) {
                $query->where('nombre', 'like', "%{$search}%");
            })
            ->when($request->institucion_id, function ($query, $institucionId) {
                $query->where('institucion_id', $institucionId);
            })
            ->when($request->estado, function ($query, $estado) {
                $query->where('estado', $estado);
            });

        $anios = $query->paginate($request->per_page ?? 10);

        return AnioResource::collection($anios);
    }

    /**
     * @OA\Post(
     *     path="/v1/anios",
     *     summary="Crea un nuevo año académico",
     *     tags={"Años"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreAnioRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Año creado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/AnioResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado",
     *     )
     * )
     */
    public function store(StoreAnioRequest $request)
    {
        $anio = Anio::create($request->validated());

        return new AnioResource($anio->load('institucion'));
    }

    /**
     * @OA\Get(
     *     path="/v1/anios/{anio}",
     *     summary="Obtiene los detalles de un año académico específico",
     *     tags={"Años"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="anio",
     *         in="path",
     *         description="ID del año académico",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del año obtenidos exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/AnioResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Año no encontrado",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado",
     *     )
     * )
     */
    public function show(Anio $anio)
    {
        return new AnioResource($anio->load('institucion'));
    }

    /**
     * @OA\Put(
     *     path="/v1/anios/{anio}",
     *     summary="Actualiza un año académico existente",
     *     tags={"Años"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="anio",
     *         in="path",
     *         description="ID del año académico a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateAnioRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Año actualizado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/AnioResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Año no encontrado",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado",
     *     )
     * )
     */
    public function update(UpdateAnioRequest $request, Anio $anio)
    {
        $anio->update($request->validated());

        return new AnioResource($anio->load('institucion'));
    }

    /**
     * @OA\Delete(
     *     path="/v1/anios/{anio}",
     *     summary="Elimina (soft delete) un año académico",
     *     tags={"Años"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="anio",
     *         in="path",
     *         description="ID del año académico a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Año eliminado exitosamente (sin contenido)",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Año no encontrado",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado",
     *     )
     * )
     */
    public function destroy(Anio $anio)
    {
        $anio->delete();

        return response()->noContent();
    }
}
