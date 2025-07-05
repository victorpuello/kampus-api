<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAsignaturaRequest;
use App\Http\Requests\UpdateAsignaturaRequest;
use App\Http\Resources\AsignaturaResource;
use App\Models\Asignatura;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Asignaturas",
 *     description="Operaciones relacionadas con la gestión de asignaturas"
 * )
 */
class AsignaturaController extends Controller
{
    /**
     * Constructor del controlador.
     * Aplica políticas de autorización a los recursos de asignatura.
     */
    public function __construct()
    {
        // Removido parent::__construct() que no está disponible en el controlador base
    }

    /**
     * @OA\Get(
     *     path="/v1/asignaturas",
     *     summary="Obtiene una lista paginada de asignaturas",
     *     tags={"Asignaturas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de asignaturas por página",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Término de búsqueda para filtrar asignaturas por nombre",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="area_id",
     *         in="query",
     *         description="ID del área para filtrar asignaturas",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de asignaturas obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/AsignaturaResource")
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
        
        $query = Asignatura::query()
            ->with(['area.institucion'])
            ->whereHas('area', function ($query) use ($user) {
                $query->where('institucion_id', $user->institucion_id);
            })
            ->when($request->search, function ($query, $search) {
                $query->where('nombre', 'like', "%{$search}%");
            })
            ->when($request->area_id, function ($query, $areaId) use ($user) {
                $query->where('area_id', $areaId)
                      ->whereHas('area', function ($subQuery) use ($user) {
                          $subQuery->where('institucion_id', $user->institucion_id);
                      });
            });

        $asignaturas = $query->paginate($request->per_page ?? 10);

        return AsignaturaResource::collection($asignaturas);
    }

    /**
     * @OA\Post(
     *     path="/v1/asignaturas",
     *     summary="Crea una nueva asignatura",
     *     tags={"Asignaturas"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreAsignaturaRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Asignatura creada exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/AsignaturaResource")
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
    public function store(StoreAsignaturaRequest $request)
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(401, 'Usuario no autenticado');
        }
        
        // Verificar que el área seleccionada pertenece a la institución del usuario
        $area = \App\Models\Area::find($request->area_id);
        if (!$area || $area->institucion_id !== $user->institucion_id) {
            abort(403, 'No tienes permisos para crear asignaturas en esta área');
        }
        
        $asignatura = Asignatura::create($request->validated());

        return new AsignaturaResource($asignatura->load(['area.institucion']));
    }

    /**
     * @OA\Get(
     *     path="/v1/asignaturas/{asignatura}",
     *     summary="Obtiene los detalles de una asignatura específica",
     *     tags={"Asignaturas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="asignatura",
     *         in="path",
     *         description="ID de la asignatura",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de la asignatura obtenidos exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/AsignaturaResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Asignatura no encontrada",
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
    public function show(Asignatura $asignatura)
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(401, 'Usuario no autenticado');
        }
        
        // Verificar que la asignatura pertenece a la institución del usuario
        if ($asignatura->area->institucion_id !== $user->institucion_id) {
            abort(403, 'No tienes permisos para acceder a esta asignatura');
        }
        
        return new AsignaturaResource($asignatura->load(['area.institucion']));
    }

    /**
     * @OA\Put(
     *     path="/v1/asignaturas/{asignatura}",
     *     summary="Actualiza una asignatura existente",
     *     tags={"Asignaturas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="asignatura",
     *         in="path",
     *         description="ID de la asignatura a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateAsignaturaRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Asignatura actualizada exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/AsignaturaResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Asignatura no encontrada",
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
    public function update(UpdateAsignaturaRequest $request, Asignatura $asignatura)
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(401, 'Usuario no autenticado');
        }
        
        // Verificar que la asignatura pertenece a la institución del usuario
        if ($asignatura->area->institucion_id !== $user->institucion_id) {
            abort(403, 'No tienes permisos para editar esta asignatura');
        }
        
        // Si se está cambiando el área, verificar que la nueva área también pertenece a la institución
        if ($request->has('area_id') && $request->area_id !== $asignatura->area_id) {
            $newArea = \App\Models\Area::find($request->area_id);
            if (!$newArea || $newArea->institucion_id !== $user->institucion_id) {
                abort(403, 'No tienes permisos para asignar esta área');
            }
        }
        
        $asignatura->update($request->validated());

        return new AsignaturaResource($asignatura->load(['area.institucion']));
    }

    /**
     * @OA\Delete(
     *     path="/v1/asignaturas/{asignatura}",
     *     summary="Elimina (soft delete) una asignatura",
     *     tags={"Asignaturas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="asignatura",
     *         in="path",
     *         description="ID de la asignatura a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Asignatura eliminada exitosamente (sin contenido)",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Asignatura no encontrada",
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
    public function destroy(Asignatura $asignatura)
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(401, 'Usuario no autenticado');
        }
        
        // Verificar que la asignatura pertenece a la institución del usuario
        if ($asignatura->area->institucion_id !== $user->institucion_id) {
            abort(403, 'No tienes permisos para eliminar esta asignatura');
        }
        
        $asignatura->delete();

        return response()->noContent();
    }
}
