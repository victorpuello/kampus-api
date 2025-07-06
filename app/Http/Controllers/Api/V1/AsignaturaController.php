<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAsignaturaRequest;
use App\Http\Requests\UpdateAsignaturaRequest;
use App\Http\Resources\AsignaturaResource;
use App\Models\Area;
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
     * Aplica middleware de permisos a los recursos de asignatura.
     */
    public function __construct()
    {
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':ver_asignaturas')->only(['index', 'show']);
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':crear_asignaturas')->only(['store']);
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':editar_asignaturas')->only(['update']);
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':eliminar_asignaturas')->only(['destroy']);
    }

    /**
     * @OA\Get(
     *     path="/v1/asignaturas",
     *     summary="Obtiene una lista paginada de asignaturas",
     *     tags={"Asignaturas"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de asignaturas por página",
     *         required=false,
     *
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Término de búsqueda para filtrar asignaturas por nombre",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="area_id",
     *         in="query",
     *         description="ID del área para filtrar asignaturas",
     *         required=false,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="institucion_id",
     *         in="query",
     *         description="ID de la institución para filtrar asignaturas",
     *         required=false,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de asignaturas obtenida exitosamente",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/AsignaturaResource")
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

        $query = Asignatura::query()
            ->with(['area.institucion'])
            ->join('areas', 'asignaturas.area_id', '=', 'areas.id')
            ->where('areas.institucion_id', $user->institucion_id)
            ->select('asignaturas.*');

        // Manejar ordenamiento especial para área
        if ($request->sort_by === 'area') {
            $direction = $request->sort_direction === 'desc' ? 'desc' : 'asc';
            $query->orderBy('areas.nombre', $direction);
        } else {
            // Aplicar ordenamiento para otras columnas con prefijo de tabla
            if ($request->sort_by) {
                $direction = $request->sort_direction === 'desc' ? 'desc' : 'asc';
                // Solo permitir ordenamiento por columnas que existen
                $allowedColumns = ['nombre', 'codigo', 'descripcion', 'porcentaje_area'];
                if (in_array($request->sort_by, $allowedColumns)) {
                    $query->orderBy('asignaturas.' . $request->sort_by, $direction);
                } else {
                    // Si la columna no existe, usar ordenamiento por defecto
                    $query->orderBy('asignaturas.nombre', 'asc');
                }
            } else {
                // Ordenamiento por defecto
                $query->orderBy('asignaturas.nombre', 'asc');
            }
        }

        // Aplicar búsqueda si se especifica
        if ($request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('asignaturas.nombre', 'like', "%{$searchTerm}%")
                  ->orWhere('asignaturas.codigo', 'like', "%{$searchTerm}%")
                  ->orWhere('asignaturas.descripcion', 'like', "%{$searchTerm}%");
            });
        }

        // Aplicar paginación
        $perPage = $request->per_page ?? 10;
        $asignaturas = $query->paginate($perPage);

        return AsignaturaResource::collection($asignaturas);
    }

    /**
     * @OA\Post(
     *     path="/v1/asignaturas",
     *     summary="Crea una nueva asignatura",
     *     tags={"Asignaturas"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/StoreAsignaturaRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Asignatura creada exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/AsignaturaResource")
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
    public function store(StoreAsignaturaRequest $request)
    {
        // Permiso verificado por middleware
        $user = auth()->user();

        $data = $request->validated();

        // Verificar que el área pertenece a la institución del usuario
        $area = Area::findOrFail($data['area_id']);
        if ($area->institucion_id !== $user->institucion_id) {
            abort(403, 'No tienes permisos para crear asignaturas en esta área');
        }

        $asignatura = Asignatura::create($data);

        return new AsignaturaResource($asignatura->load(['area.institucion']));
    }

    /**
     * @OA\Get(
     *     path="/v1/asignaturas/{asignatura}",
     *     summary="Obtiene los detalles de una asignatura específica",
     *     tags={"Asignaturas"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="asignatura",
     *         in="path",
     *         description="ID de la asignatura",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de la asignatura obtenidos exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/AsignaturaResource")
     *     ),
     *
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
        // Permiso verificado por middleware
        $user = auth()->user();

        // Verificar que la asignatura pertenece a la institución del usuario
        if ($asignatura->area->institucion_id !== $user->institucion_id) {
            abort(403, 'No tienes permisos para acceder a esta asignatura');
        }

        return new AsignaturaResource($asignatura->load(['area.institucion', 'grados']));
    }

    /**
     * @OA\Put(
     *     path="/v1/asignaturas/{asignatura}",
     *     summary="Actualiza una asignatura existente",
     *     tags={"Asignaturas"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="asignatura",
     *         in="path",
     *         description="ID de la asignatura a actualizar",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/UpdateAsignaturaRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Asignatura actualizada exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/AsignaturaResource")
     *     ),
     *
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
        // Permiso verificado por middleware
        $user = auth()->user();

        // Verificar que la asignatura pertenece a la institución del usuario
        if ($asignatura->area->institucion_id !== $user->institucion_id) {
            abort(403, 'No tienes permisos para editar esta asignatura');
        }

        // Si se está cambiando el área, verificar que la nueva área también pertenece a la institución
        if ($request->has('area_id') && $request->area_id !== $asignatura->area_id) {
            $newArea = Area::find($request->area_id);
            if (! $newArea || $newArea->institucion_id !== $user->institucion_id) {
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
     *
     *     @OA\Parameter(
     *         name="asignatura",
     *         in="path",
     *         description="ID de la asignatura a eliminar",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
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
        // Permiso verificado por middleware
        $user = auth()->user();

        // Verificar que la asignatura pertenece a la institución del usuario
        if ($asignatura->area->institucion_id !== $user->institucion_id) {
            abort(403, 'No tienes permisos para eliminar esta asignatura');
        }

        $asignatura->delete();

        return response()->noContent();
    }
}
