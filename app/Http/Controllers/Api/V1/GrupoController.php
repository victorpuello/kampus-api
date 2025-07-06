<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGrupoRequest;
use App\Http\Requests\UpdateGrupoRequest;
use App\Http\Resources\GrupoResource;
use App\Models\Anio;
use App\Models\Grado;
use App\Models\Grupo;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Grupos",
 *     description="Operaciones relacionadas con la gestión de grupos académicos"
 * )
 */
class GrupoController extends Controller
{
    /**
     * Constructor del controlador.
     * Aplica middleware de permisos a los recursos de grupo.
     */
    public function __construct()
    {
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':ver_grupos')->only(['index', 'show', 'getEstudiantes']);
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':crear_grupos')->only(['store']);
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':editar_grupos')->only(['update']);
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':eliminar_grupos')->only(['destroy']);
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':matricular_estudiantes')->only(['matricularEstudiante', 'desmatricularEstudiante', 'desvincularEstudiante', 'trasladarEstudiante']);
    }

    /**
     * @OA\Get(
     *     path="/v1/grupos",
     *     summary="Obtiene una lista paginada de grupos académicos",
     *     tags={"Grupos"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de grupos por página",
     *         required=false,
     *
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Término de búsqueda para filtrar grupos por nombre",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="anio_id",
     *         in="query",
     *         description="ID del año académico para filtrar grupos",
     *         required=false,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="grado_id",
     *         in="query",
     *         description="ID del grado para filtrar grupos",
     *         required=false,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="director_docente_id",
     *         in="query",
     *         description="ID del docente director para filtrar grupos",
     *         required=false,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de grupos obtenida exitosamente",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/GrupoResource")
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
        


        $query = Grupo::query()
            ->with(['grado.institucion', 'sede.institucion', 'anio'])
            ->withCount('estudiantes')
            ->whereHas('grado', function ($query) use ($user) {
                $query->where('institucion_id', $user->institucion_id);
            })
            ->when($request->search, function ($query, $search) {
                $query->where('nombre', 'like', "%{$search}%");
            })
            ->when($request->grado_id, function ($query, $gradoId) {
                $query->where('grado_id', $gradoId);
            })
            ->when($request->anio_id, function ($query, $anioId) {
                $query->where('anio_id', $anioId);
            });

        // Aplicar ordenamiento si se especifica
        if ($request->sort_by) {
            $direction = $request->sort_direction === 'desc' ? 'desc' : 'asc';
            
            // Manejar ordenamiento por relaciones
            switch ($request->sort_by) {
                case 'grado.nombre':
                    $query->join('grados', 'grupos.grado_id', '=', 'grados.id')
                          ->orderBy('grados.nombre', $direction)
                          ->select('grupos.*')
                          ->distinct();
                    break;
                case 'sede.nombre':
                    $query->join('sedes', 'grupos.sede_id', '=', 'sedes.id')
                          ->orderBy('sedes.nombre', $direction)
                          ->select('grupos.*')
                          ->distinct();
                    break;
                case 'anio.nombre':
                    $query->join('anios', 'grupos.anio_id', '=', 'anios.id')
                          ->orderBy('anios.nombre', $direction)
                          ->select('grupos.*')
                          ->distinct();
                    break;
                case 'director_docente.nombre':
                    $query->leftJoin('docentes', 'grupos.director_docente_id', '=', 'docentes.id')
                          ->orderBy('docentes.nombre', $direction)
                          ->select('grupos.*')
                          ->distinct();
                    break;
                case 'estudiantes_count':
                    // Para campos calculados, usar orderByRaw
                    $query->orderByRaw("(SELECT COUNT(*) FROM estudiantes WHERE estudiantes.grupo_id = grupos.id) {$direction}");
                    break;
                default:
                    // Para campos directos de la tabla grupos
                    $query->orderBy($request->sort_by, $direction);
                    break;
            }
        } else {
            // Ordenamiento por defecto
            $query->orderBy('nombre', 'asc');
        }

        $grupos = $query->paginate($request->per_page ?? 10);

        return GrupoResource::collection($grupos);
    }

    /**
     * @OA\Post(
     *     path="/v1/grupos",
     *     summary="Crea un nuevo grupo académico",
     *     tags={"Grupos"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/StoreGrupoRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Grupo creado exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/GrupoResource")
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
    public function store(StoreGrupoRequest $request)
    {
        // Permiso verificado por middleware
        $user = auth()->user();

        $data = $request->validated();

        // Verificar que el grado pertenece a la institución del usuario
        $grado = Grado::findOrFail($data['grado_id']);
        if ($grado->institucion_id !== $user->institucion_id) {
            abort(403, 'No tienes permisos para crear grupos en este grado');
        }

        // Verificar que el año académico pertenece a la institución del usuario
        $anio = Anio::findOrFail($data['anio_id']);
        if ($anio->institucion_id !== $user->institucion_id) {
            abort(403, 'No tienes permisos para crear grupos en este año académico');
        }

        $grupo = Grupo::create($data);

        return new GrupoResource($grupo->load(['grado.institucion', 'sede.institucion', 'anio']));
    }

    /**
     * @OA\Get(
     *     path="/v1/grupos/{grupo}",
     *     summary="Obtiene los detalles de un grupo académico específico",
     *     tags={"Grupos"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="grupo",
     *         in="path",
     *         description="ID del grupo académico",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del grupo obtenidos exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/GrupoResource")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Grupo no encontrado",
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
    public function show(Grupo $grupo)
    {
        // Permiso verificado por middleware
        $user = auth()->user();

        // Verificar que el grupo pertenece a la institución del usuario
        if ($grupo->grado->institucion_id !== $user->institucion_id) {
            abort(403, 'No tienes permisos para acceder a este grupo');
        }

        return new GrupoResource($grupo->load(['grado.institucion', 'sede.institucion', 'anio', 'estudiantes.user']));
    }

    /**
     * @OA\Put(
     *     path="/v1/grupos/{grupo}",
     *     summary="Actualiza un grupo académico existente",
     *     tags={"Grupos"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="grupo",
     *         in="path",
     *         description="ID del grupo académico a actualizar",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/UpdateGrupoRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Grupo actualizado exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/GrupoResource")
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Grupo no encontrado",
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
    public function update(UpdateGrupoRequest $request, Grupo $grupo)
    {
        // Permiso verificado por middleware
        $user = auth()->user();

        // Verificar que el grupo pertenece a la institución del usuario
        if ($grupo->grado->institucion_id !== $user->institucion_id) {
            abort(403, 'No tienes permisos para editar este grupo');
        }

        $grupo->update($request->validated());

        return new GrupoResource($grupo->load(['grado.institucion', 'sede.institucion', 'anio']));
    }

    /**
     * @OA\Delete(
     *     path="/v1/grupos/{grupo}",
     *     summary="Elimina (soft delete) un grupo académico",
     *     tags={"Grupos"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="grupo",
     *         in="path",
     *         description="ID del grupo académico a eliminar",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Grupo eliminado exitosamente (sin contenido)",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Grupo no encontrado",
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
    public function destroy(Grupo $grupo)
    {
        // Permiso verificado por middleware
        $user = auth()->user();

        // Verificar que el grupo pertenece a la institución del usuario
        if ($grupo->grado->institucion_id !== $user->institucion_id) {
            abort(403, 'No tienes permisos para eliminar este grupo');
        }

        $grupo->delete();

        return response()->noContent();
    }

    /**
     * @OA\Delete(
     *     path="/v1/grupos/{grupo}/estudiantes/{estudiante}",
     *     summary="Desvincula un estudiante de un grupo",
     *     tags={"Grupos"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="grupo",
     *         in="path",
     *         description="ID del grupo académico",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="estudiante",
     *         in="path",
     *         description="ID del estudiante a desvincular",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Estudiante desvinculado exitosamente",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Grupo o estudiante no encontrado",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado",
     *     )
     * )
     */
    public function desvincularEstudiante(Grupo $grupo, $estudianteId)
    {
        // Permiso verificado por middleware
        $user = auth()->user();

        // Verificar que el grupo pertenece a la institución del usuario
        if ($grupo->grado->institucion_id !== $user->institucion_id) {
            abort(403, 'No tienes permisos para acceder a este grupo');
        }

        // Buscar el estudiante en el grupo
        $estudiante = $grupo->estudiantes()->where('id', $estudianteId)->first();
        
        if (!$estudiante) {
            abort(404, 'El estudiante no está matriculado en este grupo');
        }

        // Desvincular el estudiante del grupo
        $estudiante->update(['grupo_id' => null]);

        return response()->json([
            'message' => 'Estudiante desvinculado exitosamente del grupo'
        ]);
    }

    /**
     * @OA\Put(
     *     path="/v1/grupos/{grupo}/estudiantes/{estudiante}/trasladar",
     *     summary="Traslada un estudiante de un grupo a otro",
     *     tags={"Grupos"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="grupo",
     *         in="path",
     *         description="ID del grupo de origen",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="estudiante",
     *         in="path",
     *         description="ID del estudiante a trasladar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"grupo_destino_id"},
     *             @OA\Property(property="grupo_destino_id", type="integer", description="ID del grupo destino")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Estudiante trasladado exitosamente",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Grupo o estudiante no encontrado",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *     )
     * )
     */
    public function trasladarEstudiante(Request $request, Grupo $grupo, $estudianteId)
    {
        // Permiso verificado por middleware
        $user = auth()->user();

        // Verificar que el grupo pertenece a la institución del usuario
        if ($grupo->grado->institucion_id !== $user->institucion_id) {
            abort(403, 'No tienes permisos para acceder a este grupo');
        }

        // Validar el request
        $request->validate([
            'grupo_destino_id' => 'required|integer|exists:grupos,id'
        ]);

        // Buscar el estudiante en el grupo
        $estudiante = $grupo->estudiantes()->where('id', $estudianteId)->first();
        
        if (!$estudiante) {
            abort(404, 'El estudiante no está matriculado en este grupo');
        }

        // Verificar que el grupo destino existe y pertenece a la misma institución
        $grupoDestino = Grupo::whereHas('grado', function ($query) use ($user) {
            $query->where('institucion_id', $user->institucion_id);
        })->find($request->grupo_destino_id);

        if (!$grupoDestino) {
            abort(404, 'El grupo destino no existe o no tienes permisos para acceder a él');
        }

        // Verificar que no es el mismo grupo
        if ($grupo->id === $grupoDestino->id) {
            abort(422, 'El grupo destino no puede ser el mismo grupo de origen');
        }

        // Trasladar el estudiante al nuevo grupo
        $estudiante->update(['grupo_id' => $grupoDestino->id]);

        return response()->json([
            'message' => 'Estudiante trasladado exitosamente al grupo destino',
            'grupo_destino' => [
                'id' => $grupoDestino->id,
                'nombre' => $grupoDestino->nombre,
                'grado' => [
                    'id' => $grupoDestino->grado->id,
                    'nombre' => $grupoDestino->grado->nombre
                ]
            ]
        ]);
    }
}
