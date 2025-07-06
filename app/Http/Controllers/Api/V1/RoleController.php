<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Roles",
 *     description="Operaciones relacionadas con la gestión de roles de usuarios"
 * )
 */
class RoleController extends Controller
{
    /**
     * Constructor del controlador.
     * Aplica middleware de permisos a los recursos de roles.
     */
    public function __construct()
    {
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':ver_roles')->only(['index', 'show', 'getAllRoles', 'getRolePermissions']);
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':asignar_permisos')->only(['assignRoles']);
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':crear_roles')->only(['store']);
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':editar_roles')->only(['update']);
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':eliminar_roles')->only(['destroy']);
    }

    /**
     * @OA\Get(
     *     path="/v1/users/{user}/roles",
     *     summary="Obtiene los roles de un usuario específico",
     *     tags={"Roles"},
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
     *         description="Roles del usuario obtenidos exitosamente",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/RoleResource")
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
    public function index(Request $request, $userId): JsonResponse
    {
        // Permiso verificado por middleware
        $roles = Role::with('permissions')->get();

        return response()->json(RoleResource::collection($roles));
    }

    /**
     * @OA\Get(
     *     path="/v1/users/{user}/roles/{role}",
     *     summary="Obtiene un rol específico de un usuario",
     *     tags={"Roles"},
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
     *     @OA\Parameter(
     *         name="role",
     *         in="path",
     *         description="ID del rol",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Rol obtenido exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/RoleResource")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Rol no encontrado",
     *     )
     * )
     */
    public function show(Request $request, $userId, $roleId): JsonResponse
    {
        // Permiso verificado por middleware
        $role = Role::with('permissions')->findOrFail($roleId);

        return response()->json(new RoleResource($role));
    }

    /**
     * @OA\Post(
     *     path="/v1/users/{user}/roles",
     *     summary="Asigna roles a un usuario",
     *     tags={"Roles"},
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
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"role_ids"},
     *
     *             @OA\Property(property="role_ids", type="array", @OA\Items(type="integer"), description="IDs de los roles a asignar")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Roles asignados exitosamente",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Roles asignados exitosamente"),
     *             @OA\Property(property="roles", type="array", @OA\Items(ref="#/components/schemas/RoleResource"))
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *     )
     * )
     */
    public function assignRoles(Request $request, $userId): JsonResponse
    {
        // Permiso verificado por middleware
        $request->validate([
            'role_ids' => 'required|array',
            'role_ids.*' => 'integer|exists:roles,id',
        ]);

        $targetUser = \App\Models\User::findOrFail($userId);
        $targetUser->roles()->sync($request->role_ids);

        return response()->json([
            'message' => 'Roles asignados exitosamente',
            'roles' => RoleResource::collection($targetUser->roles()->with('permissions')->get()),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/v1/roles",
     *     summary="Obtiene todos los roles disponibles",
     *     tags={"Roles"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Roles obtenidos exitosamente",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/RoleResource")
     *         )
     *     )
     * )
     */
    public function getAllRoles(): JsonResponse
    {
        // Permiso verificado por middleware
        $roles = Role::with('permissions')->get();

        return response()->json(RoleResource::collection($roles));
    }

    /**
     * @OA\Get(
     *     path="/v1/roles/{role}/permissions",
     *     summary="Obtiene los permisos de un rol específico",
     *     tags={"Roles"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="role",
     *         in="path",
     *         description="ID del rol",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Permisos del rol obtenidos exitosamente",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/PermissionResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Rol no encontrado",
     *     )
     * )
     */
    public function getRolePermissions($roleId): JsonResponse
    {
        // Permiso verificado por middleware
        $role = Role::with('permissions')->findOrFail($roleId);

        return response()->json($role->permissions);
    }

    /**
     * @OA\Post(
     *     path="/v1/roles",
     *     summary="Crea un nuevo rol",
     *     tags={"Roles"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/StoreRoleRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Rol creado exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/RoleResource")
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *     )
     * )
     */
    public function store(StoreRoleRequest $request)
    {
        // Permiso verificado por middleware
        $data = $request->validated();

        $role = Role::create($data);

        if ($request->has('permission_ids')) {
            $role->permissions()->sync($request->permission_ids);
        }

        return new RoleResource($role->load('permissions'));
    }

    /**
     * @OA\Put(
     *     path="/v1/roles/{role}",
     *     summary="Actualiza un rol existente",
     *     tags={"Roles"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="role",
     *         in="path",
     *         description="ID del rol a actualizar",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/UpdateRoleRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Rol actualizado exitosamente",
     *
     *         @OA\JsonContent(ref="#/components/schemas/RoleResource")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Rol no encontrado",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *     )
     * )
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        // Permiso verificado por middleware
        $data = $request->validated();

        $role->update($data);

        if ($request->has('permission_ids')) {
            $role->permissions()->sync($request->permission_ids);
        }

        return new RoleResource($role->load('permissions'));
    }

    /**
     * @OA\Delete(
     *     path="/v1/roles/{role}",
     *     summary="Elimina un rol",
     *     tags={"Roles"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="role",
     *         in="path",
     *         description="ID del rol a eliminar",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Rol eliminado exitosamente",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Rol eliminado exitosamente")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Rol no encontrado",
     *     )
     * )
     */
    public function destroy(Role $role)
    {
        // Permiso verificado por middleware
        $role->delete();

        return response()->json(['message' => 'Rol eliminado exitosamente']);
    }
}
