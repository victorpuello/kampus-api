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
     *     @OA\Parameter(
     *         name="disponibles_grupo",
     *         in="query",
     *         description="Filtrar solo docentes disponibles para ser directores de grupo",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="institucion_id",
     *         in="query",
     *         description="Filtrar docentes por institución",
     *         required=false,
     *         @OA\Schema(type="integer")
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
            ->with(['user.institucion'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                        ->orWhere('apellido', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->disponibles_grupo, function ($query) {
                $query->disponiblesParaGrupo();
            })
            ->when($request->institucion_id, function ($query, $institucionId) {
                $query->porInstitucion($institucionId);
            });

        $docentes = $query->paginate($request->per_page ?? 10);

        return DocenteResource::collection($docentes);
    }

    /**
     * @OA\Get(
     *     path="/v1/docentes/disponibles-grupo",
     *     summary="Obtiene docentes disponibles para ser directores de grupo",
     *     tags={"Docentes"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="institucion_id",
     *         in="query",
     *         description="Filtrar docentes por institución",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="grupo_id",
     *         in="query",
     *         description="ID del grupo (para incluir el director actual en edición)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de docentes disponibles obtenida exitosamente",
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
    public function disponiblesGrupo(Request $request)
    {
        $query = Docente::query()
            ->with(['user.institucion'])
            ->disponiblesParaGrupo()
            ->when($request->institucion_id, function ($query, $institucionId) {
                $query->porInstitucion($institucionId);
            });

        // Si se está editando un grupo, incluir el director actual
        if ($request->grupo_id) {
            $grupo = \App\Models\Grupo::find($request->grupo_id);
            if ($grupo && $grupo->director_docente_id) {
                $query->orWhere('id', $grupo->director_docente_id);
            }
        }

        $docentes = $query->get();

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
        // Separar los datos del usuario y del docente
        $userData = $request->only(['nombre', 'apellido', 'email', 'username', 'password', 'institucion_id', 'estado']);
        $docenteData = $request->only(['telefono', 'especialidad', 'fecha_contratacion', 'salario', 'horario_trabajo']);
        
        // Crear el usuario asociado al docente
        $user = User::create($userData);
        
        // Crear el docente y asociarlo al usuario
        $docenteData['user_id'] = $user->id;
        $docente = Docente::create($docenteData);

        return new DocenteResource($docente->load(['user.institucion']));
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
        return new DocenteResource($docente->load(['user.institucion']));
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
        // Separar los datos del usuario y del docente
        $userData = $request->only(['nombre', 'apellido', 'email', 'username', 'password', 'institucion_id', 'estado']);
        $docenteData = $request->only(['telefono', 'especialidad', 'fecha_contratacion', 'salario', 'horario_trabajo']);
        
        // Actualizar los datos del usuario asociado al docente
        if (!empty($userData)) {
            $docente->user->update($userData);
        }
        
        // Actualizar los datos del docente
        if (!empty($docenteData)) {
            $docente->update($docenteData);
        }

        return new DocenteResource($docente->load(['user.institucion']));
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
