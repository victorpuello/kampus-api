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
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':ver_asignaciones')->only(['index', 'show', 'porGrupo', 'porDocente', 'conflictos']);
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':crear_asignaciones')->only(['store']);
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':eliminar_asignaciones')->only(['destroy']);
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':actualizar_asignaciones')->only(['update']);
    }

    /**
     * @OA\Get(
     *     path="/v1/asignaciones",
     *     summary="Obtiene una lista paginada de asignaciones",
     *     tags={"Asignaciones"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de asignaciones por página",
     *         required=false,
     *
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *
     *     @OA\Parameter(
     *         name="docente_id",
     *         in="query",
     *         description="ID del docente para filtrar asignaciones",
     *         required=false,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="asignatura_id",
     *         in="query",
     *         description="ID de la asignatura para filtrar asignaciones",
     *         required=false,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="grupo_id",
     *         in="query",
     *         description="ID del grupo para filtrar asignaciones",
     *         required=false,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="anio_id",
     *         in="query",
     *         description="ID del año académico para filtrar asignaciones",
     *         required=false,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="periodo_id",
     *         in="query",
     *         description="ID del periodo para filtrar asignaciones",
     *         required=false,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="estado",
     *         in="query",
     *         description="Estado de la asignación",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="institucion_id",
     *         in="query",
     *         description="ID de la institución para filtrar asignaciones",
     *         required=false,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de asignaciones obtenida exitosamente",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/AsignacionResource")
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
        $query = Asignacion::query()
            ->with([
                'docente.user',
                'asignatura.area',
                'grupo.sede.institucion',
                'grupo.grado',
                'franjaHoraria',
                'anioAcademico',
                'periodo',
            ])
            ->when($request->docente_id, function ($query, $docenteId) {
                $query->where('docente_id', $docenteId);
            })
            ->when($request->asignatura_id, function ($query, $asignaturaId) {
                $query->where('asignatura_id', $asignaturaId);
            })
            ->when($request->grupo_id, function ($query, $grupoId) {
                $query->where('grupo_id', $grupoId);
            })
            ->when($request->anio_academico_id, function ($query, $anioId) {
                $query->where('anio_academico_id', $anioId);
            })
            ->when($request->periodo_id, function ($query, $periodoId) {
                $query->where('periodo_id', $periodoId);
            })
            ->when($request->estado, function ($query, $estado) {
                $query->where('estado', $estado);
            })
            ->when($request->institucion_id, function ($query, $institucionId) {
                $query->porInstitucion($institucionId);
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
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/StoreAsignacionRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Asignación creada exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/AsignacionResource")
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
    public function store(StoreAsignacionRequest $request)
    {
        $data = $request->validated();

        // Validar conflictos de horario
        $asignacion = new Asignacion($data);

        if ($asignacion->tieneConflictoDocente()) {
            return response()->json([
                'message' => 'El docente ya tiene una asignación en este horario',
                'conflicto' => 'docente',
            ], 422);
        }

        if ($asignacion->tieneConflictoGrupo()) {
            return response()->json([
                'message' => 'El grupo ya tiene una asignación en este horario',
                'conflicto' => 'grupo',
            ], 422);
        }

        $asignacion = Asignacion::create($data);

        return new AsignacionResource($asignacion->load([
            'docente.user',
            'asignatura.area',
            'grupo.sede.institucion',
            'grupo.grado',
            'franjaHoraria',
            'anioAcademico',
            'periodo',
        ]));
    }

    /**
     * @OA\Get(
     *     path="/v1/asignaciones/{asignacion}",
     *     summary="Obtiene los detalles de una asignación específica",
     *     tags={"Asignaciones"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="asignacion",
     *         in="path",
     *         description="ID de la asignación",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de la asignación obtenidos exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/AsignacionResource")
     *     ),
     *
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
     *
     *     @OA\Parameter(
     *         name="asignacion",
     *         in="path",
     *         description="ID de la asignación a actualizar",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/UpdateAsignacionRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Asignación actualizada exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/AsignacionResource")
     *     ),
     *
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
     *
     *     @OA\Parameter(
     *         name="asignacion",
     *         in="path",
     *         description="ID de la asignación a eliminar",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
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

        return response()->json(['message' => 'Asignación eliminada exitosamente']);
    }

    /**
     * @OA\Get(
     *     path="/v1/asignaciones/grupo/{grupoId}",
     *     summary="Obtiene las asignaciones de un grupo específico",
     *     tags={"Asignaciones"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="grupoId",
     *         in="path",
     *         description="ID del grupo",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Asignaciones del grupo obtenidas exitosamente",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/AsignacionResource")
     *         )
     *     )
     * )
     */
    public function porGrupo($grupoId)
    {
        $asignaciones = Asignacion::porGrupo($grupoId)
            ->activas()
            ->with([
                'docente.user',
                'asignatura.area',
                'franjaHoraria',
                'anioAcademico',
                'periodo',
            ])
            ->orderBy('dia_semana')
            ->orderBy('franja_horaria_id')
            ->get();

        return AsignacionResource::collection($asignaciones);
    }

    /**
     * @OA\Get(
     *     path="/v1/asignaciones/docente/{docenteId}",
     *     summary="Obtiene las asignaciones de un docente específico",
     *     tags={"Asignaciones"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="docenteId",
     *         in="path",
     *         description="ID del docente",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Asignaciones del docente obtenidas exitosamente",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/AsignacionResource")
     *         )
     *     )
     * )
     */
    public function porDocente($docenteId)
    {
        $asignaciones = Asignacion::porDocente($docenteId)
            ->activas()
            ->with([
                'asignatura.area',
                'grupo.sede.institucion',
                'grupo.grado',
                'franjaHoraria',
                'anioAcademico',
                'periodo',
            ])
            ->orderBy('dia_semana')
            ->orderBy('franja_horaria_id')
            ->get();

        return AsignacionResource::collection($asignaciones);
    }

    /**
     * @OA\Get(
     *     path="/v1/asignaciones/conflictos",
     *     summary="Obtiene los conflictos de horarios en las asignaciones",
     *     tags={"Asignaciones"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Conflictos obtenidos exitosamente",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="conflictos_docente", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="conflictos_grupo", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function conflictos()
    {
        $asignaciones = Asignacion::activas()
            ->with(['docente.user', 'asignatura', 'grupo', 'franjaHoraria'])
            ->get();

        $conflictosDocente = [];
        $conflictosGrupo = [];

        foreach ($asignaciones as $asignacion) {
            if ($asignacion->tieneConflictoDocente()) {
                $conflictosDocente[] = [
                    'asignacion' => new AsignacionResource($asignacion),
                    'tipo' => 'docente',
                ];
            }

            if ($asignacion->tieneConflictoGrupo()) {
                $conflictosGrupo[] = [
                    'asignacion' => new AsignacionResource($asignacion),
                    'tipo' => 'grupo',
                ];
            }
        }

        return response()->json([
            'conflictos_docente' => $conflictosDocente,
            'conflictos_grupo' => $conflictosGrupo,
        ]);
    }
}
