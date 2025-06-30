<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocenteRequest;
use App\Http\Requests\UpdateDocenteRequest;
use App\Http\Resources\DocenteResource;
use App\Models\Docente;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Docentes",
 *     description="Operaciones relacionadas con la gestión de docentes"
 * )
 */
class DocenteController extends Controller
{
    /**
     * Constructor del controlador.
     */
    public function __construct()
    {
        // Removido parent::__construct() y authorizeResource() que no están disponibles
    }

    /**
     * @OA\Get(
     *     path="/v1/docentes",
     *     summary="Obtiene una lista paginada de docentes",
     *     tags={"Docentes"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de docentes por página",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Término de búsqueda para filtrar docentes por nombre, apellido o email",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de docentes obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/DocenteResource")
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
        $query = Docente::query()
            ->with(['user'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                        ->orWhere('apellido', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });

        $docentes = $query->paginate($request->per_page ?? 10);

        return DocenteResource::collection($docentes);
    }

    /**
     * @OA\Post(
     *     path="/v1/docentes",
     *     summary="Crea un nuevo docente",
     *     tags={"Docentes"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreDocenteRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Docente creado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/DocenteResource")
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
    public function store(StoreDocenteRequest $request)
    {
        // Crear el usuario asociado al docente
        $user = User::create($request->validated());
        // Crear el docente y asociarlo al usuario
        $docente = $user->docente()->create($request->validated());

        return new DocenteResource($docente->load('user'));
    }

    /**
     * @OA\Get(
     *     path="/v1/docentes/{docente}",
     *     summary="Obtiene los detalles de un docente específico",
     *     tags={"Docentes"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="docente",
     *         in="path",
     *         description="ID del docente",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del docente obtenidos exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/DocenteResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Docente no encontrado",
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
    public function show(Docente $docente)
    {
        return new DocenteResource($docente->load('user'));
    }

    /**
     * @OA\Put(
     *     path="/v1/docentes/{docente}",
     *     summary="Actualiza un docente existente",
     *     tags={"Docentes"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="docente",
     *         in="path",
     *         description="ID del docente a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateDocenteRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Docente actualizado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/DocenteResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Docente no encontrado",
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
    public function update(UpdateDocenteRequest $request, Docente $docente)
    {
        // Actualizar los datos del usuario asociado al docente
        $docente->user->update($request->validated());
        // Actualizar los datos del docente
        $docente->update($request->validated());

        return new DocenteResource($docente->load('user'));
    }

    /**
     * @OA\Delete(
     *     path="/v1/docentes/{docente}",
     *     summary="Elimina (soft delete) un docente y su usuario asociado",
     *     tags={"Docentes"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="docente",
     *         in="path",
     *         description="ID del docente a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Docente eliminado exitosamente (sin contenido)",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Docente no encontrado",
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
    public function destroy(Docente $docente)
    {
        // Eliminar lógicamente el usuario asociado al docente
        $docente->user->delete();
        // Eliminar lógicamente el docente
        $docente->delete();

        return response()->noContent();
    }
}
