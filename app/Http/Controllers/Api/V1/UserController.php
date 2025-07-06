<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;

/**
 * @OA\Tag(
 *     name="Usuarios",
 *     description="Operaciones relacionadas con la gestión de usuarios"
 * )
 */
class UserController extends Controller
{
    /**
     * Constructor del controlador.
     */
    public function __construct()
    {
        // Constructor del controlador
    }

    /**
     * @OA\Get(
     *     path="/v1/users",
     *     summary="Obtiene una lista paginada de usuarios",
     *     tags={"Usuarios"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de usuarios por página",
     *         required=false,
     *
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de usuarios obtenida exitosamente",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/UserResource")
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
    public function index()
    {
        // Obtener todos los usuarios con sus relaciones
        $users = User::with(['institucion', 'roles.permissions'])
            ->get();

        // Devolver con la estructura esperada por el frontend
        return response()->json([
            'data' => UserResource::collection($users),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/v1/users",
     *     summary="Crea un nuevo usuario",
     *     tags={"Usuarios"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/StoreUserRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Usuario creado exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
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
    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->validated());
        $user->roles()->sync($request->roles);

        return new UserResource($user->load(['institucion', 'roles.permissions']));
    }

    /**
     * @OA\Get(
     *     path="/v1/users/{user}",
     *     summary="Obtiene los detalles de un usuario específico",
     *     tags={"Usuarios"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="ID del usuario",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del usuario obtenidos exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado",
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
    public function show(User $user)
    {
        return new UserResource($user->load(['institucion', 'roles.permissions']));
    }

    /**
     * @OA\Put(
     *     path="/v1/users/{user}",
     *     summary="Actualiza un usuario existente",
     *     tags={"Usuarios"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="ID del usuario a actualizar",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/UpdateUserRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Usuario actualizado exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado",
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
    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->validated());

        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        }

        return new UserResource($user->load(['institucion', 'roles.permissions']));
    }

    /**
     * @OA\Delete(
     *     path="/v1/users/{user}",
     *     summary="Elimina (soft delete) un usuario",
     *     tags={"Usuarios"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="ID del usuario a eliminar",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Usuario eliminado exitosamente (sin contenido)",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado",
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
    public function destroy(User $user)
    {
        $user->delete();

        return response()->noContent();
    }
}
