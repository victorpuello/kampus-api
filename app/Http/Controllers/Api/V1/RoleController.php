<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Roles",
 *     description="Operaciones relacionadas con la gestión de roles de usuarios"
 * )
 */
class RoleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/users/{user}/roles",
     *     summary="Obtiene los roles de un usuario específico",
     *     tags={"Roles"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="ID del usuario",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Roles del usuario obtenidos exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/RoleResource")
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
    public function index(Request $request, $userId): JsonResponse
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(401, 'Usuario no autenticado');
        }

        // Verificar permisos para ver roles de usuarios
        if (!$user->hasPermissionTo('ver_roles')) {
            abort(403, 'No tienes permisos para ver roles');
        }

        $roles = Role::with('permissions')->get();

        return response()->json(RoleResource::collection($roles));
    }

    /**
     * @OA\Get(
     *     path="/v1/users/{user}/roles/{role}",
     *     summary="Obtiene un rol específico de un usuario",
     *     tags={"Roles"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="ID del usuario",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="role",
     *         in="path",
     *         description="ID del rol",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rol obtenido exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/RoleResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rol no encontrado",
     *     )
     * )
     */
    public function show(Request $request, $userId, $roleId): JsonResponse
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(401, 'Usuario no autenticado');
        }

        if (!$user->hasPermissionTo('ver_roles')) {
            abort(403, 'No tienes permisos para ver roles');
        }

        $role = Role::with('permissions')->findOrFail($roleId);

        return response()->json(new RoleResource($role));
    }

    /**
     * @OA\Post(
     *     path="/v1/users/{user}/roles",
     *     summary="Asigna roles a un usuario",
     *     tags={"Roles"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="ID del usuario",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"role_ids"},
     *             @OA\Property(property="role_ids", type="array", @OA\Items(type="integer"), description="IDs de los roles a asignar")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Roles asignados exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Roles asignados exitosamente"),
     *             @OA\Property(property="roles", type="array", @OA\Items(ref="#/components/schemas/RoleResource"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *     )
     * )
     */
    public function assignRoles(Request $request, $userId): JsonResponse
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(401, 'Usuario no autenticado');
        }

        if (!$user->hasPermissionTo('asignar_permisos')) {
            abort(403, 'No tienes permisos para asignar roles');
        }

        $request->validate([
            'role_ids' => 'required|array',
            'role_ids.*' => 'integer|exists:roles,id'
        ]);

        $targetUser = \App\Models\User::findOrFail($userId);
        $targetUser->roles()->sync($request->role_ids);

        return response()->json([
            'message' => 'Roles asignados exitosamente',
            'roles' => RoleResource::collection($targetUser->roles()->with('permissions')->get())
        ]);
    }

    /**
     * @OA\Get(
     *     path="/v1/roles",
     *     summary="Obtiene todos los roles disponibles",
     *     tags={"Roles"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Roles obtenidos exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/RoleResource")
     *         )
     *     )
     * )
     */
    public function getAllRoles(): JsonResponse
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(401, 'Usuario no autenticado');
        }

        if (!$user->hasPermissionTo('ver_roles')) {
            abort(403, 'No tienes permisos para ver roles');
        }

        $roles = Role::with('permissions')->get();

        return response()->json(RoleResource::collection($roles));
    }

    /**
     * @OA\Get(
     *     path="/v1/roles/{role}/permissions",
     *     summary="Obtiene los permisos de un rol específico",
     *     tags={"Roles"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="role",
     *         in="path",
     *         description="ID del rol",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permisos del rol obtenidos exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/PermissionResource")
     *         )
     *     )
     * )
     */
    public function getRolePermissions($roleId): JsonResponse
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(401, 'Usuario no autenticado');
        }

        if (!$user->hasPermissionTo('ver_permisos')) {
            abort(403, 'No tienes permisos para ver permisos');
        }

        $role = Role::with('permissions')->findOrFail($roleId);

        return response()->json([
            'role' => new RoleResource($role),
            'permissions' => $role->permissions
        ]);
    }

    /**
     * @OA\Post(
     *     path="/v1/roles",
     *     summary="Crea un nuevo rol",
     *     tags={"Roles"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreRoleRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Rol creado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/RoleResource")
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
    public function store(StoreRoleRequest $request)
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(401, 'Usuario no autenticado');
        }
        
        // Verificar permiso para crear roles
        if (!$user->hasPermissionTo('crear_roles')) {
            abort(403, 'No tienes permisos para crear roles');
        }
        
        $data = $request->validated();
        $role = Role::create($data);

        return new RoleResource($role->load('permissions'));
    }

    /**
     * @OA\Put(
     *     path="/v1/roles/{role}",
     *     summary="Actualiza un rol existente",
     *     tags={"Roles"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="role",
     *         in="path",
     *         description="ID del rol a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateRoleRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rol actualizado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/RoleResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rol no encontrado",
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
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(401, 'Usuario no autenticado');
        }
        
        // Verificar permiso para editar roles
        if (!$user->hasPermissionTo('editar_roles')) {
            abort(403, 'No tienes permisos para editar roles');
        }
        
        $data = $request->validated();
        $role->update($data);

        return new RoleResource($role->load('permissions'));
    }

    /**
     * @OA\Delete(
     *     path="/v1/roles/{role}",
     *     summary="Elimina un rol",
     *     tags={"Roles"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="role",
     *         in="path",
     *         description="ID del rol a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rol eliminado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Rol eliminado exitosamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rol no encontrado",
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
    public function destroy(Role $role)
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(401, 'Usuario no autenticado');
        }
        
        // Verificar permiso para eliminar roles
        if (!$user->hasPermissionTo('eliminar_roles')) {
            abort(403, 'No tienes permisos para eliminar roles');
        }
        
        // Verificar que el rol no esté siendo usado por usuarios
        if ($role->users()->count() > 0) {
            abort(400, 'No se puede eliminar el rol porque está siendo usado por usuarios');
        }
        
        $role->delete();

        return response()->json([
            'message' => 'Rol eliminado exitosamente'
        ]);
    }
} 