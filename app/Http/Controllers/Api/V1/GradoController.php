<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGradoRequest;
use App\Http\Requests\UpdateGradoRequest;
use App\Http\Resources\GradoResource;
use App\Models\Grado;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Grados",
 *     description="Operaciones relacionadas con la gestión de grados académicos"
 * )
 */
class GradoController extends Controller
{
    /**
     * Constructor del controlador.
     * Aplica middleware de permisos a los recursos de grado.
     */
    public function __construct()
    {
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':ver_grados')->only(['index', 'show', 'niveles']);
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':crear_grados')->only(['store']);
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':editar_grados')->only(['update']);
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':eliminar_grados')->only(['destroy']);
    }

    /**
     * @OA\Get(
     *     path="/v1/grados",
     *     summary="Obtiene una lista paginada de grados académicos",
     *     tags={"Grados"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de grados por página",
     *         required=false,
     *
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Término de búsqueda para filtrar grados por nombre",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="institucion_id",
     *         in="query",
     *         description="ID de la institución para filtrar grados",
     *         required=false,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de grados obtenida exitosamente",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/GradoResource")
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

        $query = Grado::query()
            ->with('institucion')
            ->withCount('grupos')
            ->where('institucion_id', $user->institucion_id)
            ->when($request->search, function ($query, $search) {
                $query->where('nombre', 'like', "%{$search}%");
            });

        // Aplicar ordenamiento si se especifica
        if ($request->sort_by) {
            $direction = $request->sort_direction === 'desc' ? 'desc' : 'asc';
            $query->orderBy($request->sort_by, $direction);
        } else {
            // Ordenamiento por defecto
            $query->orderBy('nombre', 'asc');
        }

        $grados = $query->paginate($request->per_page ?? 10);

        return GradoResource::collection($grados);
    }

    /**
     * @OA\Post(
     *     path="/v1/grados",
     *     summary="Crea un nuevo grado académico",
     *     tags={"Grados"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/StoreGradoRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Grado creado exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/GradoResource")
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
    public function store(StoreGradoRequest $request)
    {
        // Permiso verificado por middleware
        $user = auth()->user();

        // Asegurar que el grado se crea para la institución del usuario
        $data = $request->validated();
        $data['institucion_id'] = $user->institucion_id;

        $grado = Grado::create($data);

        return new GradoResource($grado->load('institucion'));
    }

    /**
     * @OA\Get(
     *     path="/v1/grados/{grado}",
     *     summary="Obtiene los detalles de un grado académico específico",
     *     tags={"Grados"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="grado",
     *         in="path",
     *         description="ID del grado académico",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del grado obtenidos exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/GradoResource")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Grado no encontrado",
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
    public function show(Grado $grado)
    {
        // Permiso verificado por middleware
        $user = auth()->user();

        // Verificar que el grado pertenece a la institución del usuario
        if ($grado->institucion_id !== $user->institucion_id) {
            abort(403, 'No tienes permisos para acceder a este grado');
        }

        return new GradoResource($grado->load(['institucion', 'grupos']));
    }

    /**
     * @OA\Put(
     *     path="/v1/grados/{grado}",
     *     summary="Actualiza un grado académico existente",
     *     tags={"Grados"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="grado",
     *         in="path",
     *         description="ID del grado académico a actualizar",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/UpdateGradoRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Grado actualizado exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/GradoResource")
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Grado no encontrado",
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
    public function update(UpdateGradoRequest $request, Grado $grado)
    {
        // Permiso verificado por middleware
        $user = auth()->user();

        // Verificar que el grado pertenece a la institución del usuario
        if ($grado->institucion_id !== $user->institucion_id) {
            abort(403, 'No tienes permisos para editar este grado');
        }

        $grado->update($request->validated());

        return new GradoResource($grado->load('institucion'));
    }

    /**
     * @OA\Delete(
     *     path="/v1/grados/{grado}",
     *     summary="Elimina (soft delete) un grado académico",
     *     tags={"Grados"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="grado",
     *         in="path",
     *         description="ID del grado académico a eliminar",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Grado eliminado exitosamente (sin contenido)",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Grado no encontrado",
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
    public function destroy(Grado $grado)
    {
        // Permiso verificado por middleware
        $user = auth()->user();

        // Verificar que el grado pertenece a la institución del usuario
        if ($grado->institucion_id !== $user->institucion_id) {
            abort(403, 'No tienes permisos para eliminar este grado');
        }

        $grado->delete();

        return response()->noContent();
    }

    /**
     * @OA\Get(
     *     path="/v1/grados/niveles",
     *     summary="Obtiene la lista de niveles educativos disponibles",
     *     tags={"Grados"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de niveles obtenida exitosamente",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(type="string", example="Preescolar")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *     )
     * )
     */
    public function niveles()
    {
        return response()->json([
            'data' => Grado::getNivelesDisponibles(),
        ]);
    }
}
