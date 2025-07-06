<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFranjaHorariaRequest;
use App\Http\Requests\UpdateFranjaHorariaRequest;
use App\Http\Resources\FranjaHorariaResource;
use App\Models\FranjaHoraria;
use App\Models\Institucion;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Franjas Horarias",
 *     description="Operaciones relacionadas con la gestión de franjas horarias"
 * )
 */
class FranjaHorariaController extends Controller
{
    /**
     * Constructor del controlador.
     * Aplica políticas de autorización a los recursos de franja horaria.
     */
    public function __construct()
    {
        // Removido parent::__construct() que no está disponible en el controlador base
    }

    /**
     * @OA\Get(
     *     path="/v1/franjas-horarias",
     *     summary="Obtiene una lista paginada de franjas horarias",
     *     tags={"Franjas Horarias"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de franjas horarias por página",
     *         required=false,
     *
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *
     *     @OA\Parameter(
     *         name="institucion_id",
     *         in="query",
     *         description="ID de la institución para filtrar franjas horarias",
     *         required=false,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de franjas horarias obtenida exitosamente",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/FranjaHorariaResource")
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
        $query = FranjaHoraria::query()
            ->with('institucion')
            ->when($request->institucion_id, function ($query, $institucionId) {
                $query->where('institucion_id', $institucionId);
            });

        $franjasHorarias = $query->paginate($request->per_page ?? 10);

        return FranjaHorariaResource::collection($franjasHorarias);
    }

    /**
     * @OA\Get(
     *     path="/v1/instituciones/{institucion}/franjas-horarias",
     *     summary="Obtiene una lista paginada de franjas horarias de una institución específica",
     *     tags={"Franjas Horarias"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="institucion",
     *         in="path",
     *         description="ID de la institución",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de franjas horarias por página",
     *         required=false,
     *
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de franjas horarias obtenida exitosamente",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/FranjaHorariaResource")
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
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Institución no encontrada",
     *     )
     * )
     */
    public function indexForInstitucion(Request $request, Institucion $institucion)
    {
        $franjasHorarias = $institucion->franjasHorarias()
            ->with('institucion')
            ->paginate($request->per_page ?? 10);

        return FranjaHorariaResource::collection($franjasHorarias);
    }

    /**
     * @OA\Post(
     *     path="/v1/franjas-horarias",
     *     summary="Crea una nueva franja horaria",
     *     tags={"Franjas Horarias"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/StoreFranjaHorariaRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Franja horaria creada exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/FranjaHorariaResource")
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
    public function store(StoreFranjaHorariaRequest $request)
    {
        $franjaHoraria = FranjaHoraria::create($request->validated());

        return new FranjaHorariaResource($franjaHoraria->load('institucion'));
    }

    /**
     * @OA\Post(
     *     path="/v1/instituciones/{institucion}/franjas-horarias",
     *     summary="Crea una nueva franja horaria para una institución específica",
     *     tags={"Franjas Horarias"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="institucion",
     *         in="path",
     *         description="ID de la institución",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/StoreFranjaHorariaRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Franja horaria creada exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/FranjaHorariaResource")
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
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Institución no encontrada",
     *     )
     * )
     */
    public function storeForInstitucion(Request $request, $institucionId)
    {
        $data = $request->all();
        $data['institucion_id'] = $institucionId;
        $franja = FranjaHoraria::create($data);

        return new FranjaHorariaResource($franja);
    }

    /**
     * @OA\Get(
     *     path="/v1/franjas-horarias/{franja_horaria}",
     *     summary="Obtiene los detalles de una franja horaria específica",
     *     tags={"Franjas Horarias"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="franja_horaria",
     *         in="path",
     *         description="ID de la franja horaria",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de la franja horaria obtenidos exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/FranjaHorariaResource")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Franja horaria no encontrada",
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
    public function show(FranjaHoraria $franjaHoraria)
    {
        return new FranjaHorariaResource($franjaHoraria->load('institucion'));
    }

    /**
     * @OA\Get(
     *     path="/v1/instituciones/{institucion}/franjas-horarias/{franja_horaria}",
     *     summary="Obtiene los detalles de una franja horaria específica de una institución",
     *     tags={"Franjas Horarias"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="institucion",
     *         in="path",
     *         description="ID de la institución",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="franja_horaria",
     *         in="path",
     *         description="ID de la franja horaria",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de la franja horaria obtenidos exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/FranjaHorariaResource")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Franja horaria o institución no encontrada",
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
    public function showForInstitucion(Institucion $institucion, FranjaHoraria $franjaHoraria)
    {
        // Verificar que la franja horaria pertenece a la institución
        if ($franjaHoraria->institucion_id !== $institucion->id) {
            abort(404, 'Franja horaria no encontrada en esta institución');
        }

        return new FranjaHorariaResource($franjaHoraria->load('institucion'));
    }

    /**
     * @OA\Put(
     *     path="/v1/franjas-horarias/{franja_horaria}",
     *     summary="Actualiza una franja horaria existente",
     *     tags={"Franjas Horarias"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="franja_horaria",
     *         in="path",
     *         description="ID de la franja horaria a actualizar",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/UpdateFranjaHorariaRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Franja horaria actualizada exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/FranjaHorariaResource")
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Franja horaria no encontrada",
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
    public function update(UpdateFranjaHorariaRequest $request, FranjaHoraria $franjaHoraria)
    {
        $franjaHoraria->update($request->validated());

        return new FranjaHorariaResource($franjaHoraria->load('institucion'));
    }

    /**
     * @OA\Put(
     *     path="/v1/instituciones/{institucion}/franjas-horarias/{franja_horaria}",
     *     summary="Actualiza una franja horaria existente de una institución",
     *     tags={"Franjas Horarias"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="institucion",
     *         in="path",
     *         description="ID de la institución",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="franja_horaria",
     *         in="path",
     *         description="ID de la franja horaria a actualizar",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/UpdateFranjaHorariaRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Franja horaria actualizada exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/FranjaHorariaResource")
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Franja horaria o institución no encontrada",
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
    public function updateForInstitucion(UpdateFranjaHorariaRequest $request, Institucion $institucion, FranjaHoraria $franjaHoraria)
    {
        // Verificar que la franja horaria pertenece a la institución
        if ($franjaHoraria->institucion_id !== $institucion->id) {
            abort(404, 'Franja horaria no encontrada en esta institución');
        }

        $franjaHoraria->update($request->validated());

        return new FranjaHorariaResource($franjaHoraria->load('institucion'));
    }

    /**
     * @OA\Delete(
     *     path="/v1/franjas-horarias/{franja_horaria}",
     *     summary="Elimina (soft delete) una franja horaria",
     *     tags={"Franjas Horarias"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="franja_horaria",
     *         in="path",
     *         description="ID de la franja horaria a eliminar",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Franja horaria eliminada exitosamente (sin contenido)",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Franja horaria no encontrada",
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
    public function destroy($id)
    {
        $model = FranjaHoraria::find($id);

        if (! $model) {
            abort(404, 'Franja horaria no encontrada');
        }

        $model->delete();

        return response()->noContent();
    }

    /**
     * @OA\Delete(
     *     path="/v1/instituciones/{institucion}/franjas-horarias/{franja_horaria}",
     *     summary="Elimina (soft delete) una franja horaria de una institución",
     *     tags={"Franjas Horarias"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="institucion",
     *         in="path",
     *         description="ID de la institución",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="franja_horaria",
     *         in="path",
     *         description="ID de la franja horaria a eliminar",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Franja horaria eliminada exitosamente (sin contenido)",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Franja horaria o institución no encontrada",
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
    public function destroyForInstitucion(Institucion $institucion, FranjaHoraria $franjaHoraria)
    {
        // Verificar que la franja horaria pertenece a la institución
        if ($franjaHoraria->institucion_id !== $institucion->id) {
            abort(404, 'Franja horaria no encontrada en esta institución');
        }

        $franjaHoraria->delete();

        return response()->noContent();
    }
}
