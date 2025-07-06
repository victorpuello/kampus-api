<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSedeRequest;
use App\Http\Requests\UpdateSedeRequest;
use App\Http\Resources\SedeResource;
use App\Models\Sede;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Sedes",
 *     description="Operaciones relacionadas con la gestión de sedes"
 * )
 */
class SedeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/sedes",
     *     summary="Obtiene una lista paginada de sedes",
     *     tags={"Sedes"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de sedes por página",
     *         required=false,
     *
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Término de búsqueda para filtrar sedes por nombre",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="institucion_id",
     *         in="query",
     *         description="ID de la institución para filtrar sedes",
     *         required=false,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de sedes obtenida exitosamente",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/SedeResource")
     *         )
     *     ),
     *
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
        $query = Sede::with('institucion')
            ->when($request->search, function ($query, $search) {
                $query->where('nombre', 'like', "%{$search}%");
            })
            ->when($request->institucion_id, function ($query, $institucionId) {
                $query->where('institucion_id', $institucionId);
            });

        $sedes = $query->paginate($request->per_page ?? 10);

        return SedeResource::collection($sedes);
    }

    /**
     * @OA\Post(
     *     path="/v1/sedes",
     *     summary="Crea una nueva sede",
     *     tags={"Sedes"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/StoreSedeRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Sede creada exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/SedeResource")
     *     ),
     *
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
    public function store(StoreSedeRequest $request)
    {
        $sede = Sede::create($request->validated());

        return new SedeResource($sede->load('institucion'));
    }

    /**
     * @OA\Get(
     *     path="/v1/sedes/{sede}",
     *     summary="Obtiene los detalles de una sede específica",
     *     tags={"Sedes"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="sede",
     *         in="path",
     *         description="ID de la sede",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de la sede obtenidos exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/SedeResource")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Sede no encontrada",
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
    public function show(Sede $sede)
    {
        return new SedeResource($sede->load('institucion'));
    }

    /**
     * @OA\Put(
     *     path="/v1/sedes/{sede}",
     *     summary="Actualiza una sede existente",
     *     tags={"Sedes"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="sede",
     *         in="path",
     *         description="ID de la sede a actualizar",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/UpdateSedeRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Sede actualizada exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/SedeResource")
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Sede no encontrada",
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
    public function update(UpdateSedeRequest $request, Sede $sede)
    {
        $sede->update($request->validated());

        return new SedeResource($sede->load('institucion'));
    }

    /**
     * @OA\Delete(
     *     path="/v1/sedes/{sede}",
     *     summary="Elimina una sede",
     *     tags={"Sedes"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="sede",
     *         in="path",
     *         description="ID de la sede a eliminar",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Sede eliminada exitosamente",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Sede no encontrada",
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
    public function destroy(Sede $sede)
    {
        $sede->delete();

        return response()->noContent();
    }
}
