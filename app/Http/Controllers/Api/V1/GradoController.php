<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGradoRequest;
use App\Http\Requests\UpdateGradoRequest;
use App\Http\Resources\GradoResource;
use App\Models\Grado;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Grados",
 *     description="Operaciones relacionadas con la gestión de grados académicos"
 * )
 */
class GradoController extends Controller
{
    /**
     * Constructor del controlador.
     * Aplica políticas de autorización a los recursos de grado.
     */
    public function __construct()
    {
        parent::__construct();
        $this->authorizeResource(Grado::class, 'grado');
    }

    /**
     * @OA\Get(
     *     path="/v1/grados",
     *     summary="Obtiene una lista paginada de grados académicos",
     *     tags={"Grados"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de grados por página",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Término de búsqueda para filtrar grados por nombre",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="institucion_id",
     *         in="query",
     *         description="ID de la institución para filtrar grados",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de grados obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/GradoResource")
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
        $query = Grado::query()
            ->with('institucion')
            ->when($request->search, function ($query, $search) {
                $query->where('nombre', 'like', "%{$search}%");
            })
            ->when($request->institucion_id, function ($query, $institucionId) {
                $query->where('institucion_id', $institucionId);
            });

        $grados = $query->paginate($request->per_page ?? 10);

        return GradoResource::collection($grados);
    }

    /**
     * @OA\Post(
     *     path="/v1/grados",
     *     summary="Crea un nuevo grado académico",
     *     tags={"Grados"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreGradoRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Grado creado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/GradoResource")
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
    public function store(StoreGradoRequest $request)
    {
        $grado = Grado::create($request->validated());

        return new GradoResource($grado->load('institucion'));
    }

    /**
     * @OA\Get(
     *     path="/v1/grados/{grado}",
     *     summary="Obtiene los detalles de un grado académico específico",
     *     tags={"Grados"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="grado",
     *         in="path",
     *         description="ID del grado académico",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del grado obtenidos exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/GradoResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Grado no encontrado",
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
    public function show(Grado $grado)
    {
        return new GradoResource($grado->load('institucion'));
    }

    /**
     * @OA\Put(
     *     path="/v1/grados/{grado}",
     *     summary="Actualiza un grado académico existente",
     *     tags={"Grados"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="grado",
     *         in="path",
     *         description="ID del grado académico a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateGradoRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Grado actualizado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/GradoResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Grado no encontrado",
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
    public function update(UpdateGradoRequest $request, Grado $grado)
    {
        $grado->update($request->validated());

        return new GradoResource($grado->load('institucion'));
    }

    /**
     * @OA\Delete(
     *     path="/v1/grados/{grado}",
     *     summary="Elimina (soft delete) un grado académico",
     *     tags={"Grados"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="grado",
     *         in="path",
     *         description="ID del grado académico a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Grado eliminado exitosamente (sin contenido)",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Grado no encontrado",
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
    public function destroy(Grado $grado)
    {
        $grado->delete();

        return response()->noContent();
    }
}
