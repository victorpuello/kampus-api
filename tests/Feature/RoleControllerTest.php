<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Models\Institucion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Clase de prueba para el controlador de roles.
 *
 * Contiene pruebas que verifican las operaciones CRUD del controlador
 * de roles y la gestión de permisos asociados.
 */
class RoleControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $institution;
    protected $adminUser;
    protected $adminRole;
    protected $adminPermission;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->institution = Institucion::factory()->create();
        
        // Crear rol y permiso de administrador
        $this->adminRole = Role::factory()->create([
            'nombre' => 'Administrador',
            'descripcion' => 'Acceso total al sistema'
        ]);
        
        $this->adminPermission = Permission::factory()->create([
            'nombre' => 'ver_roles',
            'descripcion' => 'Ver lista de roles'
        ]);
        
        $this->adminRole->permissions()->attach($this->adminPermission);
        
        // Crear usuario administrador
        $this->adminUser = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => '123456',
            'institucion_id' => $this->institution->id,
            'estado' => 'activo'
        ]);
        
        $this->adminUser->roles()->attach($this->adminRole);
    }

    // ==================== PRUEBAS DE AUTENTICACIÓN ====================

    /**
     * Prueba que las rutas requieren autenticación.
     */
    public function test_routes_require_authentication()
    {
        $response = $this->getJson('/api/v1/roles');
        
        $response->assertStatus(401);
    }

    /**
     * Prueba que las rutas requieren permisos específicos.
     */
    public function test_routes_require_specific_permissions()
    {
        // Crear usuario sin permisos
        $userWithoutPermissions = User::factory()->create([
            'institucion_id' => $this->institution->id,
            'estado' => 'activo'
        ]);
        
        Sanctum::actingAs($userWithoutPermissions);

        $response = $this->getJson('/api/v1/roles');
        
        $response->assertStatus(403);
    }

    // ==================== PRUEBAS DE LISTADO ====================

    /**
     * Prueba obtener todos los roles.
     */
    public function test_can_get_all_roles()
    {
        Sanctum::actingAs($this->adminUser);

        // Crear roles adicionales
        Role::factory()->create(['nombre' => 'Docente']);
        Role::factory()->create(['nombre' => 'Estudiante']);

        $response = $this->getJson('/api/v1/roles');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'nombre',
                            'permissions'
                        ]
                    ]
                ]);

        $this->assertEquals(3, count($response->json('data')));
    }

    /**
     * Prueba obtener roles con permisos cargados.
     */
    public function test_can_get_roles_with_permissions()
    {
        Sanctum::actingAs($this->adminUser);

        // Crear permiso adicional
        $permission = Permission::factory()->create(['nombre' => 'crear_roles']);
        $this->adminRole->permissions()->attach($permission);

        $response = $this->getJson('/api/v1/roles');

        $response->assertStatus(200);

        $adminRoleData = collect($response->json('data'))
            ->firstWhere('nombre', 'Administrador');

        $this->assertNotNull($adminRoleData);
        $this->assertEquals(2, count($adminRoleData['permissions']));
    }

    // ==================== PRUEBAS DE CREACIÓN ====================

    /**
     * Prueba crear un nuevo rol.
     */
    public function test_can_create_new_role()
    {
        Sanctum::actingAs($this->adminUser);

        $roleData = [
            'nombre' => 'Nuevo Rol',
            'descripcion' => 'Descripción del nuevo rol'
        ];

        $response = $this->postJson('/api/v1/roles', $roleData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'nombre',
                        'descripcion',
                        'permissions'
                    ]
                ]);

        $this->assertDatabaseHas('roles', [
            'nombre' => 'Nuevo Rol',
            'descripcion' => 'Descripción del nuevo rol'
        ]);
    }

    /**
     * Prueba crear rol con validación de datos.
     */
    public function test_cannot_create_role_with_invalid_data()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->postJson('/api/v1/roles', [
            'nombre' => '', // Nombre vacío
            'descripcion' => 'Descripción'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['nombre']);
    }

    /**
     * Prueba que no se puede crear un rol con nombre duplicado.
     */
    public function test_cannot_create_role_with_duplicate_name()
    {
        Sanctum::actingAs($this->adminUser);

        // Crear rol existente
        Role::factory()->create(['nombre' => 'Rol Existente']);

        $response = $this->postJson('/api/v1/roles', [
            'nombre' => 'Rol Existente',
            'descripcion' => 'Descripción'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['nombre']);
    }

    // ==================== PRUEBAS DE ACTUALIZACIÓN ====================

    /**
     * Prueba actualizar un rol existente.
     */
    public function test_can_update_existing_role()
    {
        Sanctum::actingAs($this->adminUser);

        $role = Role::factory()->create([
            'nombre' => 'Rol Original',
            'descripcion' => 'Descripción original'
        ]);

        $updateData = [
            'nombre' => 'Rol Actualizado',
            'descripcion' => 'Descripción actualizada'
        ];

        $response = $this->putJson("/api/v1/roles/{$role->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'nombre' => 'Rol Actualizado',
                        'descripcion' => 'Descripción actualizada'
                    ]
                ]);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'nombre' => 'Rol Actualizado',
            'descripcion' => 'Descripción actualizada'
        ]);
    }

    /**
     * Prueba actualizar rol con datos inválidos.
     */
    public function test_cannot_update_role_with_invalid_data()
    {
        Sanctum::actingAs($this->adminUser);

        $role = Role::factory()->create(['nombre' => 'Rol Original']);

        $response = $this->putJson("/api/v1/roles/{$role->id}", [
            'nombre' => '', // Nombre vacío
            'descripcion' => 'Descripción'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['nombre']);
    }

    /**
     * Prueba actualizar rol inexistente.
     */
    public function test_cannot_update_nonexistent_role()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->putJson('/api/v1/roles/999', [
            'nombre' => 'Rol Actualizado',
            'descripcion' => 'Descripción'
        ]);

        $response->assertStatus(404);
    }

    // ==================== PRUEBAS DE ELIMINACIÓN ====================

    /**
     * Prueba eliminar un rol existente.
     */
    public function test_can_delete_existing_role()
    {
        Sanctum::actingAs($this->adminUser);

        $role = Role::factory()->create(['nombre' => 'Rol a Eliminar']);

        $response = $this->deleteJson("/api/v1/roles/{$role->id}");

        $response->assertStatus(200)
                ->assertJson(['message' => 'Rol eliminado exitosamente']);

        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    /**
     * Prueba eliminar rol inexistente.
     */
    public function test_cannot_delete_nonexistent_role()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->deleteJson('/api/v1/roles/999');

        $response->assertStatus(404);
    }

    /**
     * Prueba que al eliminar un rol se eliminan las relaciones con usuarios.
     */
    public function test_deleting_role_removes_user_relationships()
    {
        Sanctum::actingAs($this->adminUser);

        $role = Role::factory()->create(['nombre' => 'Rol Temporal']);
        $user = User::factory()->create(['institucion_id' => $this->institution->id]);
        
        $role->users()->attach($user);

        // Verificar que existe la relación
        $this->assertDatabaseHas('user_has_roles', [
            'user_id' => $user->id,
            'role_id' => $role->id
        ]);

        $response = $this->deleteJson("/api/v1/roles/{$role->id}");

        $response->assertStatus(200);

        // Verificar que se eliminó la relación
        $this->assertDatabaseMissing('user_has_roles', [
            'user_id' => $user->id,
            'role_id' => $role->id
        ]);
    }

    // ==================== PRUEBAS DE GESTIÓN DE PERMISOS ====================

    /**
     * Prueba asignar permisos a un rol.
     */
    public function test_can_assign_permissions_to_role()
    {
        Sanctum::actingAs($this->adminUser);

        $role = Role::factory()->create(['nombre' => 'Rol de Prueba']);
        $permission1 = Permission::factory()->create(['nombre' => 'permission.1']);
        $permission2 = Permission::factory()->create(['nombre' => 'permission.2']);

        $response = $this->postJson("/api/v1/roles/{$role->id}/permissions", [
            'permission_ids' => [$permission1->id, $permission2->id]
        ]);

        $response->assertStatus(200)
                ->assertJson(['message' => 'Permisos asignados exitosamente']);

        $this->assertEquals(2, $role->fresh()->permissions->count());
        $this->assertTrue($role->fresh()->permissions->contains($permission1));
        $this->assertTrue($role->fresh()->permissions->contains($permission2));
    }

    /**
     * Prueba sincronizar permisos de un rol.
     */
    public function test_can_sync_role_permissions()
    {
        Sanctum::actingAs($this->adminUser);

        $role = Role::factory()->create(['nombre' => 'Rol de Prueba']);
        $permission1 = Permission::factory()->create(['nombre' => 'permission.1']);
        $permission2 = Permission::factory()->create(['nombre' => 'permission.2']);
        $permission3 = Permission::factory()->create(['nombre' => 'permission.3']);

        // Asignar permisos iniciales
        $role->permissions()->attach([$permission1->id, $permission2->id]);

        $response = $this->putJson("/api/v1/roles/{$role->id}/permissions", [
            'permission_ids' => [$permission2->id, $permission3->id]
        ]);

        $response->assertStatus(200)
                ->assertJson(['message' => 'Permisos sincronizados exitosamente']);

        $this->assertEquals(2, $role->fresh()->permissions->count());
        $this->assertFalse($role->fresh()->permissions->contains($permission1));
        $this->assertTrue($role->fresh()->permissions->contains($permission2));
        $this->assertTrue($role->fresh()->permissions->contains($permission3));
    }

    /**
     * Prueba obtener permisos de un rol específico.
     */
    public function test_can_get_role_permissions()
    {
        Sanctum::actingAs($this->adminUser);

        $role = Role::factory()->create(['nombre' => 'Rol de Prueba']);
        $permission = Permission::factory()->create(['nombre' => 'test.permission']);
        $role->permissions()->attach($permission);

        $response = $this->getJson("/api/v1/roles/{$role->id}/permissions");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'role' => [
                        'id',
                        'nombre',
                        'permissions'
                    ],
                    'permissions' => [
                        '*' => [
                            'id',
                            'nombre',
                            'descripcion'
                        ]
                    ]
                ]);

        $this->assertEquals(1, count($response->json('permissions')));
    }

    // ==================== PRUEBAS DE ASIGNACIÓN DE ROLES A USUARIOS ====================

    /**
     * Prueba asignar roles a un usuario.
     */
    public function test_can_assign_roles_to_user()
    {
        Sanctum::actingAs($this->adminUser);

        $user = User::factory()->create(['institucion_id' => $this->institution->id]);
        $role1 = Role::factory()->create(['nombre' => 'Rol 1']);
        $role2 = Role::factory()->create(['nombre' => 'Rol 2']);

        $response = $this->postJson("/api/v1/users/{$user->id}/roles", [
            'role_ids' => [$role1->id, $role2->id]
        ]);

        $response->assertStatus(200)
                ->assertJson(['message' => 'Roles asignados exitosamente']);

        $this->assertEquals(2, $user->fresh()->roles->count());
        $this->assertTrue($user->fresh()->hasRole('Rol 1'));
        $this->assertTrue($user->fresh()->hasRole('Rol 2'));
    }

    /**
     * Prueba obtener roles de un usuario específico.
     */
    public function test_can_get_user_roles()
    {
        Sanctum::actingAs($this->adminUser);

        $user = User::factory()->create(['institucion_id' => $this->institution->id]);
        $role = Role::factory()->create(['nombre' => 'Rol de Usuario']);
        $user->roles()->attach($role);

        $response = $this->getJson("/api/v1/users/{$user->id}/roles");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'nombre',
                            'permissions'
                        ]
                    ]
                ]);

        $this->assertEquals(1, count($response->json('data')));
    }

    // ==================== PRUEBAS DE BÚSQUEDA Y FILTRADO ====================

    /**
     * Prueba buscar roles por nombre.
     */
    public function test_can_search_roles_by_name()
    {
        Sanctum::actingAs($this->adminUser);

        Role::factory()->create(['nombre' => 'Docente']);
        Role::factory()->create(['nombre' => 'Estudiante']);
        Role::factory()->create(['nombre' => 'Administrador']);

        $response = $this->getJson('/api/v1/roles?search=Docente');

        $response->assertStatus(200);

        $roles = $response->json('data');
        $this->assertEquals(1, count($roles));
        $this->assertEquals('Docente', $roles[0]['nombre']);
    }

    /**
     * Prueba paginación de roles.
     */
    public function test_can_paginate_roles()
    {
        Sanctum::actingAs($this->adminUser);

        // Crear múltiples roles
        for ($i = 1; $i <= 15; $i++) {
            Role::factory()->create(['nombre' => "Rol {$i}"]);
        }

        $response = $this->getJson('/api/v1/roles?per_page=10');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data',
                    'links',
                    'meta'
                ]);

        $this->assertEquals(10, count($response->json('data')));
    }

    // ==================== PRUEBAS DE PERMISOS ESPECÍFICOS ====================

    /**
     * Prueba que se requieren permisos específicos para cada operación.
     */
    public function test_requires_specific_permissions_for_operations()
    {
        // Crear usuario con permisos limitados
        $limitedUser = User::factory()->create([
            'institucion_id' => $this->institution->id,
            'estado' => 'activo'
        ]);

        $limitedRole = Role::factory()->create(['nombre' => 'Usuario Limitado']);
        $viewPermission = Permission::factory()->create(['nombre' => 'ver_roles']);
        
        $limitedRole->permissions()->attach($viewPermission);
        $limitedUser->roles()->attach($limitedRole);

        Sanctum::actingAs($limitedUser);

        // Debería poder ver roles
        $response = $this->getJson('/api/v1/roles');
        $response->assertStatus(200);

        // No debería poder crear roles
        $response = $this->postJson('/api/v1/roles', [
            'nombre' => 'Nuevo Rol',
            'descripcion' => 'Descripción'
        ]);
        $response->assertStatus(403);

        // No debería poder actualizar roles
        $role = Role::factory()->create();
        $response = $this->putJson("/api/v1/roles/{$role->id}", [
            'nombre' => 'Rol Actualizado'
        ]);
        $response->assertStatus(403);

        // No debería poder eliminar roles
        $response = $this->deleteJson("/api/v1/roles/{$role->id}");
        $response->assertStatus(403);
    }

    // ==================== PRUEBAS DE VALIDACIÓN DE DATOS ====================

    /**
     * Prueba validación de datos en creación de roles.
     */
    public function test_role_creation_data_validation()
    {
        Sanctum::actingAs($this->adminUser);

        // Nombre muy largo
        $response = $this->postJson('/api/v1/roles', [
            'nombre' => str_repeat('a', 51), // Más de 50 caracteres
            'descripcion' => 'Descripción'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['nombre']);

        // Sin nombre
        $response = $this->postJson('/api/v1/roles', [
            'descripcion' => 'Descripción'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['nombre']);
    }

    /**
     * Prueba validación de datos en asignación de permisos.
     */
    public function test_permission_assignment_data_validation()
    {
        Sanctum::actingAs($this->adminUser);

        $role = Role::factory()->create();

        // Sin permission_ids
        $response = $this->postJson("/api/v1/roles/{$role->id}/permissions", []);
        $response->assertStatus(422);

        // permission_ids no es array
        $response = $this->postJson("/api/v1/roles/{$role->id}/permissions", [
            'permission_ids' => 'not_an_array'
        ]);
        $response->assertStatus(422);

        // permission_ids con IDs inválidos
        $response = $this->postJson("/api/v1/roles/{$role->id}/permissions", [
            'permission_ids' => [999, 1000]
        ]);
        $response->assertStatus(422);
    }
} 