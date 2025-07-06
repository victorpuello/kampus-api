<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInstitucionRequest;
use App\Http\Requests\UpdateInstitucionRequest;
use App\Http\Resources\InstitucionResource;
use App\Http\Resources\SedeResource;
use App\Models\Institucion;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Instituciones",
 *     description="Operaciones relacionadas con la gestión de instituciones"
 * )
 */
class InstitucionController extends Controller
{
    /**
     * Constructor del controlador.
     * Aplica políticas de autorización a los recursos de institución.
     */
    public function __construct()
    {
        // Removido parent::__construct() que no está disponible en el controlador base
    }

    /**
     * @OA\Get(
     *     path="/v1/instituciones",
     *     summary="Obtiene una lista paginada de instituciones",
     *     tags={"Instituciones"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de instituciones por página",
     *         required=false,
     *
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Término de búsqueda para filtrar instituciones por nombre o siglas",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de instituciones obtenida exitosamente",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/InstitucionResource")
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
        $query = Institucion::query()
            ->when($request->search, function ($query, $search) {
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('siglas', 'like', "%{$search}%");
            });

        $instituciones = $query->paginate($request->per_page ?? 10);

        return InstitucionResource::collection($instituciones);
    }

    /**
     * @OA\Post(
     *     path="/v1/instituciones",
     *     summary="Crea una nueva institución",
     *     tags={"Instituciones"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/StoreInstitucionRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Institución creada exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/InstitucionResource")
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
    public function store(StoreInstitucionRequest $request)
    {
        $data = $request->validated();

        // Crear la institución sin el archivo
        $institucion = Institucion::create($data);

        // Configurar campos de archivo
        $institucion->setFileFields(['escudo']);
        $institucion->setFilePaths(['escudo' => 'instituciones/escudos']);

        // Manejar la carga del escudo si se proporciona
        if ($request->hasFile('escudo')) {
            try {
                \Log::info('🔄 Intentando subir escudo en store', [
                    'institucion_id' => $institucion->id,
                    'file_size' => $request->file('escudo')->getSize(),
                    'file_name' => $request->file('escudo')->getClientOriginalName(),
                ]);

                $result = $institucion->uploadFile($request->file('escudo'), 'escudo', [
                    'resize' => true,
                    'width' => 300,
                    'height' => 300,
                    'quality' => 85,
                ]);

                if (! $result) {
                    throw new \Exception('El método uploadFile retornó false');
                }

                \Log::info('✅ Escudo subido exitosamente', [
                    'institucion_id' => $institucion->id,
                    'escudo_path' => $institucion->escudo,
                ]);

            } catch (\Exception $e) {
                \Log::error('❌ Error al subir escudo en store', [
                    'institucion_id' => $institucion->id,
                    'error' => $e->getMessage(),
                ]);

                // Limpiar el campo escudo si la carga falla
                $institucion->escudo = null;
                $institucion->save();
                // Si falla la carga del archivo, eliminar la institución creada
                $institucion->delete();

                return response()->json([
                    'message' => 'Error al cargar el escudo: '.$e->getMessage(),
                ], 422);
            }
        }

        return new InstitucionResource($institucion);
    }

    /**
     * @OA\Get(
     *     path="/v1/instituciones/{institucion}",
     *     summary="Obtiene los detalles de una institución específica",
     *     tags={"Instituciones"},
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
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de la institución obtenidos exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/InstitucionResource")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Institución no encontrada",
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
    public function show(Request $request, Institucion $institucion)
    {
        // Debug: Log de la petición
        \Log::info('🔍 Petición GET institución', [
            'id' => $institucion->id,
            'nombre' => $institucion->nombre,
            'escudo' => $institucion->escudo,
            'escudo_url' => $institucion->getFileUrl('escudo'),
            'request_url' => $request->fullUrl(),
            'user_agent' => $request->userAgent(),
            'headers' => $request->headers->all(),
        ]);

        if ($request->has('include') && str_contains($request->include, 'sedes')) {
            $institucion->load('sedes');
        }

        return new InstitucionResource($institucion);
    }

    /**
     * @OA\Get(
     *     path="/v1/instituciones/{institucion}/sedes",
     *     summary="Obtiene todas las sedes de una institución específica",
     *     tags={"Instituciones"},
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
     *         description="Número de sedes por página",
     *         required=false,
     *
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Término de búsqueda para filtrar sedes por nombre o dirección",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de sedes de la institución obtenida exitosamente",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/SedeResource")),
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="last_page", type="integer"),
     *             @OA\Property(property="per_page", type="integer"),
     *             @OA\Property(property="total", type="integer")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Institución no encontrada",
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
    public function sedes(Request $request, Institucion $institucion)
    {
        $query = $institucion->sedes()
            ->when($request->search, function ($query, $search) {
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('direccion', 'like', "%{$search}%");
            });

        $sedes = $query->paginate($request->per_page ?? 10);

        return SedeResource::collection($sedes);
    }

    /**
     * @OA\Put(
     *     path="/v1/instituciones/{institucion}",
     *     summary="Actualiza una institución existente",
     *     tags={"Instituciones"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="institucion",
     *         in="path",
     *         description="ID de la institución a actualizar",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/UpdateInstitucionRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Institución actualizada exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/InstitucionResource")
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Institución no encontrada",
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
    public function update(UpdateInstitucionRequest $request, Institucion $institucion)
    {
        $data = $request->validated();

        // Configurar campos de archivo ANTES de cualquier operación
        $institucion->setFileFields(['escudo']);
        $institucion->setFilePaths(['escudo' => 'instituciones/escudos']);

        // Manejar la carga del escudo si se proporciona
        if ($request->hasFile('escudo')) {
            try {
                $result = $institucion->uploadFile($request->file('escudo'), 'escudo', [
                    'resize' => true,
                    'width' => 300,
                    'height' => 300,
                    'quality' => 85,
                ]);

                if (! $result) {
                    throw new \Exception('El método uploadFile retornó false');
                }

                // Remover el campo escudo de los datos ya que se maneja por separado
                unset($data['escudo']);
            } catch (\Exception $e) {
                \Log::error('❌ Error al actualizar escudo', [
                    'institucion_id' => $institucion->id,
                    'error' => $e->getMessage(),
                ]);

                // Limpiar el campo escudo si la carga falla
                $institucion->escudo = null;
                $institucion->save();

                return response()->json([
                    'message' => 'Error al cargar el escudo: '.$e->getMessage(),
                ], 422);
            }
        }

        // Actualizar los demás campos
        $institucion->update($data);

        return new InstitucionResource($institucion);
    }

    /**
     * @OA\Delete(
     *     path="/v1/instituciones/{institucion}",
     *     summary="Elimina (soft delete) una institución",
     *     tags={"Instituciones"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="institucion",
     *         in="path",
     *         description="ID de la institución a eliminar",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Institución eliminada exitosamente (sin contenido)",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Institución no encontrada",
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
    public function destroy(Institucion $institucion)
    {
        $institucion->delete();

        return response()->noContent();
    }
}
