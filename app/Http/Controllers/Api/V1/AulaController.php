<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAulaRequest;
use App\Http\Requests\UpdateAulaRequest;
use App\Http\Resources\AulaResource;
use App\Models\Aula;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Aulas",
 *     description="Operaciones relacionadas con la gestión de aulas"
 * )
 */
class AulaController extends Controller
{
    /**
     * Constructor del controlador.
     * Aplica políticas de autorización a los recursos de aula.
     */
    public function __construct()
    {
        parent::__construct();
        $this->authorizeResource(Aula::class, 'aula');
    }

    /**
     * @OA\Get(
     *     path="/v1/aulas",
     *     summary="Obtiene una lista paginada de aulas",
     *     tags={"Aulas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de aulas por página",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Término de búsqueda para filtrar aulas por nombre",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="institucion_id",
     *         in="query",
     *         description="ID de la institución para filtrar aulas",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de aulas obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/AulaResource")
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
        $query = Aula::query()
            ->with('institucion')
            ->when($request->search, function ($query, $search) {
                $query->where('nombre', 'like', "%{$search}%");
            })
            ->when($request->institucion_id, function ($query, $institucionId) {
                $query->where('institucion_id', $institucionId);
            });

        $aulas = $query->paginate($request->per_page ?? 10);

        return AulaResource::collection($aulas);
    }

    /**
     * @OA\Post(
     *     path="/v1/aulas",
     *     summary="Crea una nueva aula",
     *     tags={"Aulas"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreAulaRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Aula creada exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/AulaResource")
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
    public function store(StoreAulaRequest $request)
    {
        $aula = Aula::create($request->validated());

        return new AulaResource($aula->load('institucion'));
    }

    /**
     * @OA\Get(
     *     path="/v1/aulas/{aula}",
     *     summary="Obtiene los detalles de un aula específica",
     *     tags={"Aulas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="aula",
     *         in="path",
     *         description="ID del aula",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del aula obtenidos exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/AulaResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Aula no encontrada",
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
    public function show(Aula $aula)
    {
        return new AulaResource($aula->load('institucion'));
    }

    /**
     * @OA\Put(
     *     path="/v1/aulas/{aula}",
     *     summary="Actualiza un aula existente",
     *     tags={"Aulas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="aula",
     *         in="path",
     *         description="ID del aula a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateAulaRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Aula actualizada exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/AulaResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Aula no encontrada",
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
    public function update(UpdateAulaRequest $request, Aula $aula)
    {
        $aula->update($request->validated());

        return new AulaResource($aula->load('institucion'));
    }

    /**
     * @OA\Delete(
     *     path="/v1/aulas/{aula}",
     *     summary="Elimina (soft delete) un aula",
     *     tags={"Aulas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="aula",
     *         in="path",
     *         description="ID del aula a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Aula eliminada exitosamente (sin contenido)",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Aula no encontrada",
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
    public function destroy(Aula $aula)
    {
        $aula->delete();

        return response()->noContent();
    }
}
