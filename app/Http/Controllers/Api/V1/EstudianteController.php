<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEstudianteRequest;
use App\Http\Requests\UpdateEstudianteRequest;
use App\Http\Resources\EstudianteResource;
use App\Models\Estudiante;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class EstudianteController extends Controller
{
    /**
     * Constructor del controlador.
     * Aplica middleware de permisos a los recursos de estudiante.
     */
    public function __construct()
    {
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':ver_estudiantes')->only(['index', 'show']);
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':crear_estudiantes')->only(['store']);
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':editar_estudiantes')->only(['update']);
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':eliminar_estudiantes')->only(['destroy']);
    }

    /**
     * @OA\Get(
     *     path="/v1/estudiantes",
     *     summary="Obtiene una lista paginada de estudiantes",
     *     tags={"Estudiantes"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de estudiantes por página",
     *         required=false,
     *
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Término de búsqueda para filtrar estudiantes por nombre o documento",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="grupo_id",
     *         in="query",
     *         description="ID del grupo para filtrar estudiantes",
     *         required=false,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de estudiantes obtenida exitosamente",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/EstudianteResource")
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

        $query = Estudiante::query()
            ->with(['user', 'grupos.grado.institucion'])
            ->whereHas('user', function ($query) use ($user) {
                $query->where('institucion_id', $user->institucion_id);
            })
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                        ->orWhere('apellido', 'like', "%{$search}%")
                        ->orWhere('documento', 'like', "%{$search}%");
                });
            })
            ->when($request->grupo_id, function ($query, $grupoId) {
                $query->whereHas('grupos', function ($q) use ($grupoId) {
                    $q->where('grupo_id', $grupoId);
                });
            });

        $estudiantes = $query->paginate($request->per_page ?? 10);

        return EstudianteResource::collection($estudiantes);
    }

    /**
     * @OA\Post(
     *     path="/v1/estudiantes",
     *     summary="Crea un nuevo estudiante",
     *     tags={"Estudiantes"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/StoreEstudianteRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Estudiante creado exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/EstudianteResource")
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
    public function store(StoreEstudianteRequest $request)
    {
        // Permiso verificado por middleware
        $user = auth()->user();

        $data = $request->validated();

        // Crear el usuario asociado al estudiante
        $userData = [
            'name' => $data['nombre'].' '.$data['apellido'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'institucion_id' => $user->institucion_id,
        ];

        $newUser = User::create($userData);

        // Asignar rol de estudiante
        $estudianteRole = Role::where('name', 'estudiante')->first();
        if ($estudianteRole) {
            $newUser->roles()->attach($estudianteRole->id);
        }

        // Crear el estudiante
        $estudianteData = array_merge($data, ['user_id' => $newUser->id]);
        $estudiante = Estudiante::create($estudianteData);

        return new EstudianteResource($estudiante->load(['user', 'grupos.grado.institucion']));
    }

    /**
     * @OA\Get(
     *     path="/v1/estudiantes/{estudiante}",
     *     summary="Obtiene los detalles de un estudiante específico",
     *     tags={"Estudiantes"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="estudiante",
     *         in="path",
     *         description="ID del estudiante",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del estudiante obtenidos exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/EstudianteResource")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Estudiante no encontrado",
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
    public function show(Estudiante $estudiante)
    {
        // Permiso verificado por middleware
        $user = auth()->user();

        // Verificar que el estudiante pertenece a la institución del usuario
        if ($estudiante->user->institucion_id !== $user->institucion_id) {
            abort(403, 'No tienes permisos para acceder a este estudiante');
        }

        return new EstudianteResource($estudiante->load(['user', 'grupos.grado.institucion', 'acudientes']));
    }

    /**
     * @OA\Put(
     *     path="/v1/estudiantes/{estudiante}",
     *     summary="Actualiza un estudiante existente",
     *     tags={"Estudiantes"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="estudiante",
     *         in="path",
     *         description="ID del estudiante a actualizar",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/UpdateEstudianteRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Estudiante actualizado exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/EstudianteResource")
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Estudiante no encontrado",
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
    public function update(UpdateEstudianteRequest $request, Estudiante $estudiante)
    {
        // Permiso verificado por middleware
        $user = auth()->user();

        // Verificar que el estudiante pertenece a la institución del usuario
        if ($estudiante->user->institucion_id !== $user->institucion_id) {
            abort(403, 'No tienes permisos para editar este estudiante');
        }

        $data = $request->validated();

        // Actualizar el usuario asociado
        if (isset($data['nombre']) || isset($data['apellido'])) {
            $userData = [];
            if (isset($data['nombre']) && isset($data['apellido'])) {
                $userData['name'] = $data['nombre'].' '.$data['apellido'];
            }
            if (isset($data['email'])) {
                $userData['email'] = $data['email'];
            }
            if (isset($data['password'])) {
                $userData['password'] = bcrypt($data['password']);
            }

            if (! empty($userData)) {
                $estudiante->user->update($userData);
            }
        }

        // Actualizar el estudiante
        $estudiante->update($data);

        return new EstudianteResource($estudiante->load(['user', 'grupos.grado.institucion', 'acudientes']));
    }

    /**
     * @OA\Delete(
     *     path="/v1/estudiantes/{estudiante}",
     *     summary="Elimina (soft delete) un estudiante",
     *     tags={"Estudiantes"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="estudiante",
     *         in="path",
     *         description="ID del estudiante a eliminar",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Estudiante eliminado exitosamente (sin contenido)",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Estudiante no encontrado",
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
    public function destroy(Estudiante $estudiante)
    {
        // Permiso verificado por middleware
        $user = auth()->user();

        // Verificar que el estudiante pertenece a la institución del usuario
        if ($estudiante->user->institucion_id !== $user->institucion_id) {
            abort(403, 'No tienes permisos para eliminar este estudiante');
        }

        $estudiante->delete();

        return response()->noContent();
    }
}
