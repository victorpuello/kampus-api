<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAsignacionRequest;
use App\Http\Requests\UpdateAsignacionRequest;
use App\Http\Resources\AsignacionResource;
use App\Models\Asignacion;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Asignaciones",
 *     description="Operaciones relacionadas con la gestión de asignaciones de docentes a asignaturas y grupos"
 * )
 */
class AsignacionController extends Controller
{
    /**
     * Constructor del controlador.
     * Aplica políticas de autorización a los recursos de asignación.
     */
    public function __construct()
    {
        parent::__construct();
        $this->authorizeResource(Asignacion::class, 'asignacion');
    }

    /**
     * @OA\Get(
     *     path="/v1/asignaciones",
     *     summary="Obtiene una lista paginada de asignaciones",
     *     tags={"Asignaciones"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de asignaciones por página",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="docente_id",
     *         in="query",
     *         description="ID del docente para filtrar asignaciones",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="asignatura_id",
     *         in="query",
     *         description="ID de la asignatura para filtrar asignaciones",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="grupo_id",
     *         in="query",
     *         description="ID del grupo para filtrar asignaciones",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="anio_id",
     *         in="query",
     *         description="ID del año académico para filtrar asignaciones",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de asignaciones obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/AsignacionResource")
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
        $query = Asignacion::query()
            ->with(['docente.user', 'asignatura.area', 'grupo.anio', 'grupo.grado'])
            ->when($request->docente_id, function ($query, $docenteId) {
                $query->where('docente_id', $docenteId);
            })
            ->when($request->asignatura_id, function ($query, $asignaturaId) {
                $query->where('asignatura_id', $asignaturaId);
            })
            ->when($request->grupo_id, function ($query, $grupoId) {
                $query->where('grupo_id', $grupoId);
            })
            ->when($request->anio_id, function ($query, $anioId) {
                $query->where('anio_id', $anioId);
            });

        $asignaciones = $query->paginate($request->per_page ?? 10);

        return AsignacionResource::collection($asignaciones);
    }

    /**
     * @OA\Post(
     *     path="/v1/asignaciones",
     *     summary="Crea una nueva asignación",
     *     tags={"Asignaciones"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreAsignacionRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Asignación creada exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/AsignacionResource")
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
    public function store(StoreAsignacionRequest $request)
    {
        $asignacion = Asignacion::create($request->validated());

        return new AsignacionResource($asignacion->load(['docente.user', 'asignatura.area', 'grupo.anio', 'grupo.grado']));
    }

    /**
     * @OA\Get(
     *     path="/v1/asignaciones/{asignacion}",
     *     summary="Obtiene los detalles de una asignación específica",
     *     tags={"Asignaciones"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="asignacion",
     *         in="path",
     *         description="ID de la asignación",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de la asignación obtenidos exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/AsignacionResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Asignación no encontrada",
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
    public function show(Asignacion $asignacion)
    {
        return new AsignacionResource($asignacion->load(['docente.user', 'asignatura.area', 'grupo.anio', 'grupo.grado']));
    }

    /**
     * @OA\Put(
     *     path="/v1/asignaciones/{asignacion}",
     *     summary="Actualiza una asignación existente",
     *     tags={"Asignaciones"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="asignacion",
     *         in="path",
     *         description="ID de la asignación a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateAsignacionRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Asignación actualizada exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/AsignacionResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Asignación no encontrada",
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
    public function update(UpdateAsignacionRequest $request, Asignacion $asignacion)
    {
        $asignacion->update($request->validated());

        return new AsignacionResource($asignacion->load(['docente.user', 'asignatura.area', 'grupo.anio', 'grupo.grado']));
    }

    /**
     * @OA\Delete(
     *     path="/v1/asignaciones/{asignacion}",
     *     summary="Elimina (soft delete) una asignación",
     *     tags={"Asignaciones"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="asignacion",
     *         in="path",
     *         description="ID de la asignación a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Asignación eliminada exitosamente (sin contenido)",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Asignación no encontrada",
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
    public function destroy(Asignacion $asignacion)
    {
        $asignacion->delete();

        return response()->noContent();
    }
}
