<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGrupoRequest;
use App\Http\Requests\UpdateGrupoRequest;
use App\Http\Resources\GrupoResource;
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
     * Aplica políticas de autorización a los recursos de grupo.
     */
    public function __construct()
    {
        // Removido parent::__construct() que no está disponible en el controlador base
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
        $query = Grupo::query()
            ->with(['anio', 'grado', 'sede', 'directorDocente.user'])
            ->when($request->search, function ($query, $search) {
                $query->where('nombre', 'like', "%{$search}%");
            })
            ->when($request->anio_id, function ($query, $anioId) {
                $query->where('anio_id', $anioId);
            })
            ->when($request->grado_id, function ($query, $gradoId) {
                $query->where('grado_id', $gradoId);
            })
            ->when($request->director_docente_id, function ($query, $directorDocenteId) {
                $query->where('director_docente_id', $directorDocenteId);
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
        try {
            $grupo = Grupo::create($request->validated());

            return new GrupoResource($grupo->load(['anio', 'grado', 'sede', 'directorDocente.user']));
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
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
        return new GrupoResource($grupo->load(['anio', 'grado', 'sede', 'directorDocente.user', 'estudiantes.user']));
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
        try {
            $grupo->update($request->validated());

            return new GrupoResource($grupo->load(['anio', 'grado', 'sede', 'directorDocente.user']));
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
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
        $grupo->delete();

        return response()->noContent();
    }
}
