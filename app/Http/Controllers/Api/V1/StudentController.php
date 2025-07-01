<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Estudiante;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Estudiantes",
 *     description="Operaciones relacionadas con la gestión de estudiantes"
 * )
 */
class StudentController extends Controller
{
    /**
     * Constructor del controlador.
     */
    public function __construct()
    {

    }

    /**
     * @OA\Get(
     *     path="/v1/estudiantes",
     *     summary="Obtiene una lista paginada de estudiantes",
     *     tags={"Estudiantes"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de estudiantes por página",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Término de búsqueda para filtrar estudiantes por código, nombre, apellido o email",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de estudiantes obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/StudentResource")
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
        $query = Estudiante::query()
            ->with(['user', 'institucion', 'acudientes', 'acudiente'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('codigo_estudiantil', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->where('nombre', 'like', "%{$search}%")
                                ->orWhere('apellido', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            });

        $estudiantes = $query->paginate($request->per_page ?? 10);

        return StudentResource::collection($estudiantes);
    }

    /**
     * @OA\Post(
     *     path="/v1/estudiantes",
     *     summary="Crea un nuevo estudiante",
     *     tags={"Estudiantes"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreStudentRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Estudiante creado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/StudentResource")
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
    public function store(StoreStudentRequest $request)
    {
        $validatedData = $request->validated();
        
        // Separar datos del usuario y del estudiante
        $userData = array_intersect_key($validatedData, array_flip([
            'nombre', 'apellido', 'tipo_documento', 'numero_documento', 
            'email', 'username', 'password'
        ]));
        
        $studentData = array_intersect_key($validatedData, array_flip([
            'codigo_estudiantil', 'fecha_nacimiento', 'genero', 'direccion', 
            'telefono', 'institucion_id', 'estado'
        ]));
        
        // Crear el usuario asociado al estudiante
        $user = User::create($userData);
        
        // Crear el estudiante y asociarlo al usuario
        $estudiante = $user->estudiante()->create($studentData);
        
        // Manejar la relación con el acudiente si se proporciona
        if (isset($validatedData['acudiente_id']) && $validatedData['acudiente_id']) {
            $estudiante->acudientes()->attach($validatedData['acudiente_id']);
        }

        return new StudentResource($estudiante->load(['user', 'institucion', 'acudientes', 'acudiente']));
    }

    /**
     * @OA\Get(
     *     path="/v1/estudiantes/{estudiante}",
     *     summary="Obtiene los detalles de un estudiante específico",
     *     tags={"Estudiantes"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="estudiante",
     *         in="path",
     *         description="ID del estudiante",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del estudiante obtenidos exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/StudentResource")
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
    public function show(Estudiante $estudiante)
    {
        return new StudentResource($estudiante->load(['user', 'institucion', 'acudientes', 'acudiente']));
    }

    /**
     * @OA\Put(
     *     path="/v1/estudiantes/{estudiante}",
     *     summary="Actualiza un estudiante existente",
     *     tags={"Estudiantes"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="estudiante",
     *         in="path",
     *         description="ID del estudiante a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateStudentRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estudiante actualizado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/StudentResource")
     *     ),
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
    public function update(UpdateStudentRequest $request, Estudiante $estudiante)
    {
        $validatedData = $request->validated();
        
        // Separar datos del usuario y del estudiante
        $userData = array_intersect_key($validatedData, array_flip([
            'nombre', 'apellido', 'tipo_documento', 'numero_documento', 
            'email', 'username', 'password'
        ]));
        
        $studentData = array_intersect_key($validatedData, array_flip([
            'codigo_estudiantil', 'fecha_nacimiento', 'genero', 'direccion', 
            'telefono', 'institucion_id', 'estado'
        ]));
        
        // Actualizar los datos del usuario asociado al estudiante
        if (!empty($userData)) {
            $estudiante->user->update($userData);
        }
        
        // Actualizar los datos del estudiante
        if (!empty($studentData)) {
            $estudiante->update($studentData);
        }
        
        // Manejar la relación con el acudiente si se proporciona
        if (isset($validatedData['acudiente_id'])) {
            if ($validatedData['acudiente_id']) {
                // Agregar el acudiente si no existe
                $estudiante->acudientes()->syncWithoutDetaching([$validatedData['acudiente_id']]);
            } else {
                // Remover todos los acudientes si se envía null
                $estudiante->acudientes()->detach();
            }
        }

        return new StudentResource($estudiante->load(['user', 'institucion', 'acudientes', 'acudiente']));
    }

    /**
     * @OA\Delete(
     *     path="/v1/estudiantes/{estudiante}",
     *     summary="Elimina (soft delete) un estudiante y su usuario asociado",
     *     tags={"Estudiantes"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="estudiante",
     *         in="path",
     *         description="ID del estudiante a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
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
        // Eliminar lógicamente el usuario asociado al estudiante
        $estudiante->user->delete();
        // Eliminar lógicamente el estudiante
        $estudiante->delete();

        return response()->noContent();
    }
}
 