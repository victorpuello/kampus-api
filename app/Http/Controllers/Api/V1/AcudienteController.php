<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAcudienteRequest;
use App\Http\Requests\UpdateAcudienteRequest;
use App\Http\Resources\AcudienteResource;
use App\Models\Acudiente;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Acudientes",
 *     description="Operaciones relacionadas con la gestión de acudientes"
 * )
 */
class AcudienteController extends Controller
{
    /**
     * Constructor del controlador.
     * Aplica políticas de autorización a los recursos de acudiente.
     */
    public function __construct()
    {
        // Removido parent::__construct() que no está disponible en el controlador base
    }

    /**
     * @OA\Get(
     *     path="/v1/acudientes",
     *     summary="Obtiene una lista paginada de acudientes",
     *     tags={"Acudientes"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de acudientes por página",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Término de búsqueda para filtrar acudientes por nombre, email o teléfono",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de acudientes obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/AcudienteResource")
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
        $query = Acudiente::query()
            ->with('user')
            ->when($request->search, function ($query, $search) {
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('telefono', 'like', "%{$search}%");
            });

        $acudientes = $query->paginate($request->per_page ?? 10);

        return AcudienteResource::collection($acudientes);
    }

    /**
     * @OA\Post(
     *     path="/v1/acudientes",
     *     summary="Crea un nuevo acudiente",
     *     tags={"Acudientes"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreAcudienteRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Acudiente creado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/AcudienteResource")
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
    public function store(StoreAcudienteRequest $request)
    {
        $acudiente = Acudiente::create($request->validated());

        return new AcudienteResource($acudiente->load('user'));
    }

    /**
     * @OA\Get(
     *     path="/v1/acudientes/{acudiente}",
     *     summary="Obtiene los detalles de un acudiente específico",
     *     tags={"Acudientes"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="acudiente",
     *         in="path",
     *         description="ID del acudiente",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del acudiente obtenidos exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/AcudienteResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Acudiente no encontrado",
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
    public function show(Acudiente $acudiente)
    {
        return new AcudienteResource($acudiente->load('user'));
    }

    /**
     * @OA\Put(
     *     path="/v1/acudientes/{acudiente}",
     *     summary="Actualiza un acudiente existente",
     *     tags={"Acudientes"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="acudiente",
     *         in="path",
     *         description="ID del acudiente a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateAcudienteRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Acudiente actualizado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/AcudienteResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Acudiente no encontrado",
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
    public function update(UpdateAcudienteRequest $request, Acudiente $acudiente)
    {
        $acudiente->update($request->validated());

        return new AcudienteResource($acudiente->load('user'));
    }

    /**
     * @OA\Delete(
     *     path="/v1/acudientes/{acudiente}",
     *     summary="Elimina (soft delete) un acudiente",
     *     tags={"Acudientes"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="acudiente",
     *         in="path",
     *         description="ID del acudiente a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Acudiente eliminado exitosamente (sin contenido)",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Acudiente no encontrado",
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
    public function destroy(Acudiente $acudiente)
    {
        $acudiente->delete();

        return response()->noContent();
    }
}
