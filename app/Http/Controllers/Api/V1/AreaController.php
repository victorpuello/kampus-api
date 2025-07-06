<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAreaRequest;
use App\Http\Requests\UpdateAreaRequest;
use App\Http\Resources\AreaResource;
use App\Models\Area;
use App\Traits\HasServerPagination;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Áreas",
 *     description="Operaciones relacionadas con la gestión de áreas académicas"
 * )
 */
class AreaController extends Controller
{
    use HasServerPagination;

    /**
     * Constructor del controlador.
     * Aplica middleware de permisos a los recursos de área.
     */
    public function __construct()
    {
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':ver_areas')->only(['index', 'show']);
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':crear_areas')->only(['store']);
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':editar_areas')->only(['update']);
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':eliminar_areas')->only(['destroy']);
    }

    /**
     * @OA\Get(
     *     path="/v1/areas",
     *     summary="Obtiene una lista paginada de áreas académicas",
     *     tags={"Áreas"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de áreas por página",
     *         required=false,
     *
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Término de búsqueda para filtrar áreas por nombre",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Columna por la cual ordenar",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="sort_direction",
     *         in="query",
     *         description="Dirección del ordenamiento (asc o desc)",
     *         required=false,
     *
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de áreas obtenida exitosamente",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/AreaResource")
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
        // Permiso verificado por middleware
        $user = auth()->user();

        $query = Area::query()
            ->where('institucion_id', $user->institucion_id);

        $areas = $this->applyServerPaginationWithCount(
            $query,
            $request,
            ['asignaturas'], // Conteo de asignaturas
            ['nombre', 'descripcion'], // Columnas buscables
            ['nombre' => 'asc'] // Ordenamiento por defecto
        );

        return AreaResource::collection($areas);
    }

    /**
     * @OA\Post(
     *     path="/v1/areas",
     *     summary="Crea una nueva área académica",
     *     tags={"Áreas"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/StoreAreaRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Área creada exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/AreaResource")
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
    public function store(StoreAreaRequest $request)
    {
        // Permiso verificado por middleware
        $user = auth()->user();

        // Asegurar que el área se crea para la institución del usuario
        $data = $request->validated();
        $data['institucion_id'] = $user->institucion_id;

        $area = Area::create($data);

        return new AreaResource($area->load('institucion'));
    }

    /**
     * @OA\Get(
     *     path="/v1/areas/{area}",
     *     summary="Obtiene los detalles de un área académica específica",
     *     tags={"Áreas"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="area",
     *         in="path",
     *         description="ID del área académica",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del área obtenidos exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/AreaResource")
     *     ),
     *
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
        // Permiso verificado por middleware
        $user = auth()->user();

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
     *
     *     @OA\Parameter(
     *         name="area",
     *         in="path",
     *         description="ID del área académica a actualizar",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/UpdateAreaRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Área actualizada exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/AreaResource")
     *     ),
     *
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
        // Permiso verificado por middleware
        $user = auth()->user();

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
     *
     *     @OA\Parameter(
     *         name="area",
     *         in="path",
     *         description="ID del área académica a eliminar",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
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
        // Permiso verificado por middleware
        $user = auth()->user();

        // Verificar que el área pertenece a la institución del usuario
        if ($area->institucion_id !== $user->institucion_id) {
            abort(403, 'No tienes permisos para eliminar esta área');
        }

        $area->delete();

        return response()->noContent();
    }
}
