<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAreaRequest;
use App\Http\Requests\UpdateAreaRequest;
use App\Http\Resources\AreaResource;
use App\Models\Area;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Áreas",
 *     description="Operaciones relacionadas con la gestión de áreas académicas"
 * )
 */
class AreaController extends Controller
{
    /**
     * Constructor del controlador.
     * Aplica políticas de autorización a los recursos de área.
     */
    public function __construct()
    {
        // Removido parent::__construct() que no está disponible en el controlador base
    }

    /**
     * @OA\Get(
     *     path="/v1/areas",
     *     summary="Obtiene una lista paginada de áreas académicas",
     *     tags={"Áreas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de áreas por página",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Término de búsqueda para filtrar áreas por nombre",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="institucion_id",
     *         in="query",
     *         description="ID de la institución para filtrar áreas",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de áreas obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/AreaResource")
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
        $user = auth()->user();
        
        if (!$user) {
            abort(401, 'Usuario no autenticado');
        }
        
        $query = Area::query()
            ->with('institucion')
            ->where('institucion_id', $user->institucion_id)
            ->when($request->search, function ($query, $search) {
                $query->where('nombre', 'like', "%{$search}%");
            });

        $areas = $query->paginate($request->per_page ?? 10);

        return AreaResource::collection($areas);
    }

    /**
     * @OA\Post(
     *     path="/v1/areas",
     *     summary="Crea una nueva área académica",
     *     tags={"Áreas"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreAreaRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Área creada exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/AreaResource")
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
    public function store(StoreAreaRequest $request)
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(401, 'Usuario no autenticado');
        }
        
        // Asegurar que el área se crea para la institución del usuario
        $data = $request->validated();
        $data['institucion_id'] = $user->institucion_id;
        
        $area = Area::create($data);

        return new AreaResource($area->load(['institucion', 'asignaturas']));
    }

    /**
     * @OA\Get(
     *     path="/v1/areas/{area}",
     *     summary="Obtiene los detalles de un área académica específica",
     *     tags={"Áreas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="area",
     *         in="path",
     *         description="ID del área académica",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del área obtenidos exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/AreaResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Área no encontrada",
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
    public function show(Area $area)
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(401, 'Usuario no autenticado');
        }
        
        // Verificar que el área pertenece a la institución del usuario
        if ($area->institucion_id !== $user->institucion_id) {
            abort(403, 'No tienes permisos para acceder a esta área');
        }
        
        return new AreaResource($area->load(['institucion', 'asignaturas']));
    }

    /**
     * @OA\Put(
     *     path="/v1/areas/{area}",
     *     summary="Actualiza un área académica existente",
     *     tags={"Áreas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="area",
     *         in="path",
     *         description="ID del área académica a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateAreaRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Área actualizada exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/AreaResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Área no encontrada",
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
    public function update(UpdateAreaRequest $request, Area $area)
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(401, 'Usuario no autenticado');
        }
        
        // Verificar que el área pertenece a la institución del usuario
        if ($area->institucion_id !== $user->institucion_id) {
            abort(403, 'No tienes permisos para editar esta área');
        }
        
        $area->update($request->validated());

        return new AreaResource($area->load(['institucion', 'asignaturas']));
    }

    /**
     * @OA\Delete(
     *     path="/v1/areas/{area}",
     *     summary="Elimina (soft delete) un área académica",
     *     tags={"Áreas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="area",
     *         in="path",
     *         description="ID del área académica a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Área eliminada exitosamente (sin contenido)",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Área no encontrada",
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
    public function destroy(Area $area)
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(401, 'Usuario no autenticado');
        }
        
        // Verificar que el área pertenece a la institución del usuario
        if ($area->institucion_id !== $user->institucion_id) {
            abort(403, 'No tienes permisos para eliminar esta área');
        }
        
        $area->delete();

        return response()->noContent();
    }
}
