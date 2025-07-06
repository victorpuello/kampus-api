<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGrupoRequest;
use App\Http\Requests\UpdateGrupoRequest;
use App\Http\Resources\GrupoResource;
use App\Models\Grupo;
use App\Models\Grado;
use App\Models\Anio;
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
        $this->middleware(\App\Http\Middleware\CheckPermission::class . ':ver_grupos')->only(['index', 'show', 'getEstudiantes']);
        $this->middleware(\App\Http\Middleware\CheckPermission::class . ':crear_grupos')->only(['store']);
        $this->middleware(\App\Http\Middleware\CheckPermission::class . ':editar_grupos')->only(['update']);
        $this->middleware(\App\Http\Middleware\CheckPermission::class . ':eliminar_grupos')->only(['destroy']);
        $this->middleware(\App\Http\Middleware\CheckPermission::class . ':matricular_estudiantes')->only(['matricularEstudiante', 'desmatricularEstudiante']);
    }

    /**
     * @OA\Get(
     *     path="/v1/grupos",
     *     summary="Obtiene una lista paginada de grupos académicos",
     *     tags={"Grupos"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de grupos por página",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Término de búsqueda para filtrar grupos por nombre",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="anio_id",
     *         in="query",
     *         description="ID del año académico para filtrar grupos",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="grado_id",
     *         in="query",
     *         description="ID del grado para filtrar grupos",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="director_docente_id",
     *         in="query",
     *         description="ID del docente director para filtrar grupos",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de grupos obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/GrupoResource")
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
        // Permiso verificado por middleware
        $user = auth()->user();
        
        $query = Grupo::query()
            ->with(['grado.institucion', 'anio'])
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

        $grupos = $query->paginate($request->per_page ?? 10);

        return GrupoResource::collection($grupos);
    }

    /**
     * @OA\Post(
     *     path="/v1/grupos",
     *     summary="Crea un nuevo grupo académico",
     *     tags={"Grupos"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreGrupoRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Grupo creado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/GrupoResource")
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

        return new GrupoResource($grupo->load(['grado.institucion', 'anio']));
    }

    /**
     * @OA\Get(
     *     path="/v1/grupos/{grupo}",
     *     summary="Obtiene los detalles de un grupo académico específico",
     *     tags={"Grupos"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="grupo",
     *         in="path",
     *         description="ID del grupo académico",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del grupo obtenidos exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/GrupoResource")
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
    public function show(Grupo $grupo)
    {
        // Permiso verificado por middleware
        $user = auth()->user();
        
        // Verificar que el grupo pertenece a la institución del usuario
        if ($grupo->grado->institucion_id !== $user->institucion_id) {
            abort(403, 'No tienes permisos para acceder a este grupo');
        }
        
        return new GrupoResource($grupo->load(['grado.institucion', 'anio', 'estudiantes']));
    }

    /**
     * @OA\Put(
     *     path="/v1/grupos/{grupo}",
     *     summary="Actualiza un grupo académico existente",
     *     tags={"Grupos"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="grupo",
     *         in="path",
     *         description="ID del grupo académico a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateGrupoRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Grupo actualizado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/GrupoResource")
     *     ),
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

        return new GrupoResource($grupo->load(['grado.institucion', 'anio']));
    }

    /**
     * @OA\Delete(
     *     path="/v1/grupos/{grupo}",
     *     summary="Elimina (soft delete) un grupo académico",
     *     tags={"Grupos"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="grupo",
     *         in="path",
     *         description="ID del grupo académico a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
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
}
