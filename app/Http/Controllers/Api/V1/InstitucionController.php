<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInstitucionRequest;
use App\Http\Requests\UpdateInstitucionRequest;
use App\Http\Resources\InstitucionResource;
use App\Models\Institucion;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Instituciones",
 *     description="Operaciones relacionadas con la gestión de instituciones"
 * )
 */
class InstitucionController extends Controller
{
    /**
     * Constructor del controlador.
     * Aplica políticas de autorización a los recursos de institución.
     */
    public function __construct()
    {
        // Removido parent::__construct() que no está disponible en el controlador base
    }

    /**
     * @OA\Get(
     *     path="/v1/instituciones",
     *     summary="Obtiene una lista paginada de instituciones",
     *     tags={"Instituciones"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de instituciones por página",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Término de búsqueda para filtrar instituciones por nombre o siglas",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de instituciones obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/InstitucionResource")
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
        $query = Institucion::query()
            ->when($request->search, function ($query, $search) {
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('siglas', 'like', "%{$search}%");
            });

        $instituciones = $query->paginate($request->per_page ?? 10);

        return InstitucionResource::collection($instituciones);
    }

    /**
     * @OA\Post(
     *     path="/v1/instituciones",
     *     summary="Crea una nueva institución",
     *     tags={"Instituciones"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreInstitucionRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Institución creada exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/InstitucionResource")
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
    public function store(StoreInstitucionRequest $request)
    {
        $institucion = Institucion::create($request->validated());

        return new InstitucionResource($institucion);
    }

    /**
     * @OA\Get(
     *     path="/v1/instituciones/{institucion}",
     *     summary="Obtiene los detalles de una institución específica",
     *     tags={"Instituciones"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="institucion",
     *         in="path",
     *         description="ID de la institución",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de la institución obtenidos exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/InstitucionResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Institución no encontrada",
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
    public function show(Institucion $institucion)
    {
        return new InstitucionResource($institucion);
    }

    /**
     * @OA\Put(
     *     path="/v1/instituciones/{institucion}",
     *     summary="Actualiza una institución existente",
     *     tags={"Instituciones"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="institucion",
     *         in="path",
     *         description="ID de la institución a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateInstitucionRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Institución actualizada exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/InstitucionResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Institución no encontrada",
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
    public function update(UpdateInstitucionRequest $request, Institucion $institucion)
    {
        $institucion->update($request->validated());

        return new InstitucionResource($institucion);
    }

    /**
     * @OA\Delete(
     *     path="/v1/instituciones/{institucion}",
     *     summary="Elimina (soft delete) una institución",
     *     tags={"Instituciones"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="institucion",
     *         in="path",
     *         description="ID de la institución a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Institución eliminada exitosamente (sin contenido)",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Institución no encontrada",
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
    public function destroy(Institucion $institucion)
    {
        $institucion->delete();

        return response()->noContent();
    }
}
