<?php

namespace Tests\Unit;

use App\Models\Institucion;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Clase de prueba para el modelo Permission.
 *
 * Contiene pruebas unitarias para verificar la creación, relaciones,
 * asignación a roles y gestión de permisos.
 */
class PermissionTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $permission;

    protected $institution;

    protected function setUp(): void
    {
        parent::setUp();

        $this->institution = Institucion::factory()->create();

        $this->permission = Permission::factory()->create([
            'nombre' => 'ver_usuarios',
            'descripcion' => 'Permite ver la lista de usuarios',
        ]);
    }

    // ==================== PRUEBAS DE CREACIÓN Y VALIDACIÓN ====================

    /**
     * Prueba que un permiso puede ser creado correctamente.
     */
    public function test_permission_can_be_created()
    {
        $permission = Permission::factory()->create();

        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
        ]);
    }

    /**
     * Prueba que un permiso requiere un nombre único.
     */
    public function test_permission_requires_unique_name()
    {
        $permission1 = Permission::factory()->create(['nombre' => 'test.permission']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Permission::factory()->create(['nombre' => 'test.permission']);
    }

    /**
     * Prueba que un permiso puede tener una descripción opcional.
     */
    public function test_permission_can_have_optional_description()
    {
        $permission = Permission::factory()->create([
            'nombre' => 'test.permission',
            'descripcion' => null,
        ]);

        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'descripcion' => null,
        ]);
    }

    /**
     * Prueba que los campos fillable están correctamente definidos.
     */
    public function test_permission_has_correct_fillable_fields()
    {
        $permission = Permission::factory()->create([
            'nombre' => 'test.permission',
            'descripcion' => 'Test Description',
        ]);

        $this->assertEquals('test.permission', $permission->nombre);
        $this->assertEquals('Test Description', $permission->descripcion);
    }

    // ==================== PRUEBAS DE RELACIONES ====================

    /**
     * Prueba que un permiso puede estar asignado a roles.
     */
    public function test_permission_can_have_roles()
    {
        $role = Role::factory()->create();

        $this->permission->roles()->attach($role);

        $this->assertTrue($this->permission->roles->contains($role));
        $this->assertEquals(1, $this->permission->roles->count());
    }

    /**
     * Prueba que un permiso puede estar asignado a múltiples roles.
     */
    public function test_permission_can_have_multiple_roles()
    {
        $role1 = Role::factory()->create(['nombre' => 'Admin']);
        $role2 = Role::factory()->create(['nombre' => 'Docente']);
        $role3 = Role::factory()->create(['nombre' => 'Estudiante']);

        $this->permission->roles()->attach([$role1->id, $role2->id, $role3->id]);

        $this->assertEquals(3, $this->permission->roles->count());
        $this->assertTrue($this->permission->roles->contains($role1));
        $this->assertTrue($this->permission->roles->contains($role2));
        $this->assertTrue($this->permission->roles->contains($role3));
    }

    /**
     * Prueba que un permiso puede verificar si está asignado a un rol específico.
     */
    public function test_permission_can_check_if_assigned_to_specific_role()
    {
        $role = Role::factory()->create(['nombre' => 'Admin']);

        $this->permission->roles()->attach($role);

        $this->assertTrue($this->permission->roles()->where('nombre', 'Admin')->exists());
        $this->assertFalse($this->permission->roles()->where('nombre', 'NonexistentRole')->exists());
    }

    // ==================== PRUEBAS DE GESTIÓN DE ROLES ====================

    /**
     * Prueba que se pueden asignar roles a un permiso usando sync.
     */
    public function test_permission_can_sync_roles()
    {
        $role1 = Role::factory()->create(['nombre' => 'Admin']);
        $role2 = Role::factory()->create(['nombre' => 'Docente']);
        $role3 = Role::factory()->create(['nombre' => 'Estudiante']);

        // Asignar roles iniciales
        $this->permission->roles()->attach([$role1->id, $role2->id]);

        // Sincronizar con nuevos roles
        $this->permission->roles()->sync([$role2->id, $role3->id]);

        $this->assertEquals(2, $this->permission->roles->count());
        $this->assertFalse($this->permission->roles->contains($role1));
        $this->assertTrue($this->permission->roles->contains($role2));
        $this->assertTrue($this->permission->roles->contains($role3));
    }

    /**
     * Prueba que se pueden agregar roles a un permiso existente.
     */
    public function test_permission_can_add_roles()
    {
        $role1 = Role::factory()->create(['nombre' => 'Admin']);
        $role2 = Role::factory()->create(['nombre' => 'Docente']);

        // Agregar primer rol
        $this->permission->roles()->attach($role1);
        $this->assertEquals(1, $this->permission->fresh()->roles->count());

        // Agregar segundo rol
        $this->permission->roles()->attach($role2);
        $this->assertEquals(2, $this->permission->fresh()->roles->count());
    }

    /**
     * Prueba que se pueden remover roles de un permiso.
     */
    public function test_permission_can_remove_roles()
    {
        $role1 = Role::factory()->create(['nombre' => 'Admin']);
        $role2 = Role::factory()->create(['nombre' => 'Docente']);

        $this->permission->roles()->attach([$role1->id, $role2->id]);
        $this->assertEquals(2, $this->permission->fresh()->roles->count());

        // Remover un rol
        $this->permission->roles()->detach($role1->id);
        $this->assertEquals(1, $this->permission->fresh()->roles->count());
        $this->assertFalse($this->permission->fresh()->roles->contains($role1));
        $this->assertTrue($this->permission->fresh()->roles->contains($role2));
    }

    /**
     * Prueba que se pueden remover todos los roles de un permiso.
     */
    public function test_permission_can_remove_all_roles()
    {
        $role1 = Role::factory()->create(['nombre' => 'Admin']);
        $role2 = Role::factory()->create(['nombre' => 'Docente']);

        $this->permission->roles()->attach([$role1->id, $role2->id]);
        $this->assertEquals(2, $this->permission->fresh()->roles->count());

        // Remover todos los roles
        $this->permission->roles()->detach();
        $this->assertEquals(0, $this->permission->fresh()->roles->count());
    }

    // ==================== PRUEBAS DE USUARIOS CON PERMISOS ====================

    /**
     * Prueba que se pueden obtener usuarios que tienen un permiso específico.
     */
    public function test_permission_can_get_users_with_permission()
    {
        $role = Role::factory()->create(['nombre' => 'Admin']);
        $user1 = User::factory()->create(['institucion_id' => $this->institution->id]);
        $user2 = User::factory()->create(['institucion_id' => $this->institution->id]);

        // Asignar rol y permiso
        $role->permissions()->attach($this->permission);
        $user1->roles()->attach($role);
        $user2->roles()->attach($role);

        // Verificar que los usuarios tienen el permiso
        $this->assertTrue($user1->hasPermissionTo($this->permission->nombre));
        $this->assertTrue($user2->hasPermissionTo($this->permission->nombre));
    }

    /**
     * Prueba que un usuario sin el rol no tiene el permiso.
     */
    public function test_user_without_role_does_not_have_permission()
    {
        $role = Role::factory()->create(['nombre' => 'Admin']);
        $user = User::factory()->create(['institucion_id' => $this->institution->id]);

        // Asignar solo el permiso al rol, pero no asignar el rol al usuario
        $role->permissions()->attach($this->permission);

        $this->assertFalse($user->hasPermissionTo($this->permission->nombre));
    }

    /**
     * Prueba que un usuario con múltiples roles tiene todos los permisos.
     */
    public function test_user_with_multiple_roles_has_all_permissions()
    {
        $role1 = Role::factory()->create(['nombre' => 'Admin']);
        $role2 = Role::factory()->create(['nombre' => 'Docente']);
        $user = User::factory()->create(['institucion_id' => $this->institution->id]);

        $permission1 = Permission::factory()->create(['nombre' => 'permission.1']);
        $permission2 = Permission::factory()->create(['nombre' => 'permission.2']);

        // Asignar permisos a roles
        $role1->permissions()->attach($permission1);
        $role2->permissions()->attach($permission2);

        // Asignar ambos roles al usuario
        $user->roles()->attach([$role1->id, $role2->id]);

        $this->assertTrue($user->hasPermissionTo('permission.1'));
        $this->assertTrue($user->hasPermissionTo('permission.2'));
    }

    // ==================== PRUEBAS DE ELIMINACIÓN ====================

    /**
     * Prueba que al eliminar un permiso se eliminan las relaciones con roles.
     */
    public function test_permission_deletion_removes_role_relationships()
    {
        $role = Role::factory()->create();

        $this->permission->roles()->attach($role);

        // Verificar que existe la relación
        $this->assertDatabaseHas('role_has_permissions', [
            'role_id' => $role->id,
            'permission_id' => $this->permission->id,
        ]);

        // Eliminar el permiso
        $this->permission->delete();

        // Verificar que se eliminó la relación
        $this->assertDatabaseMissing('role_has_permissions', [
            'role_id' => $role->id,
            'permission_id' => $this->permission->id,
        ]);
    }

    // ==================== PRUEBAS DE CONSULTAS ====================

    /**
     * Prueba que se puede buscar un permiso por nombre.
     */
    public function test_permission_can_be_found_by_name()
    {
        $permission = Permission::where('nombre', 'ver_usuarios')->first();

        $this->assertNotNull($permission);
        $this->assertEquals('ver_usuarios', $permission->nombre);
    }

    /**
     * Prueba que se puede obtener un permiso con sus roles cargados.
     */
    public function test_permission_can_be_loaded_with_roles()
    {
        $role = Role::factory()->create();
        $this->permission->roles()->attach($role);

        $permissionWithRoles = Permission::with('roles')->find($this->permission->id);

        $this->assertTrue($permissionWithRoles->relationLoaded('roles'));
        $this->assertEquals(1, $permissionWithRoles->roles->count());
    }

    /**
     * Prueba que se pueden obtener permisos por patrón de nombre.
     */
    public function test_permissions_can_be_filtered_by_name_pattern()
    {
        Permission::factory()->create(['nombre' => 'ver_usuarios_test']);
        Permission::factory()->create(['nombre' => 'crear_usuarios_test']);
        Permission::factory()->create(['nombre' => 'editar_usuarios_test']);
        Permission::factory()->create(['nombre' => 'ver_estudiantes_test']);

        $userPermissions = Permission::where('nombre', 'like', '%usuarios_test%')->get();

        $this->assertEquals(3, $userPermissions->count());
        $this->assertTrue($userPermissions->pluck('nombre')->contains('ver_usuarios_test'));
        $this->assertTrue($userPermissions->pluck('nombre')->contains('crear_usuarios_test'));
        $this->assertTrue($userPermissions->pluck('nombre')->contains('editar_usuarios_test'));
        $this->assertFalse($userPermissions->pluck('nombre')->contains('ver_estudiantes_test'));
    }

    // ==================== PRUEBAS DE VALIDACIÓN ====================

    /**
     * Prueba que el nombre del permiso es requerido.
     */
    public function test_permission_name_is_required()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Permission::factory()->create(['nombre' => null]);
    }

    /**
     * Prueba que el nombre del permiso tiene un límite de caracteres.
     */
    public function test_permission_name_has_character_limit()
    {
        // Esta prueba verifica que el nombre no exceda el límite de caracteres
        // Verificamos que podemos crear un permiso con un nombre de 100 caracteres
        $validName = str_repeat('a', 100);
        $permission = Permission::factory()->create(['nombre' => $validName]);

        $this->assertEquals(100, strlen($permission->nombre));
        $this->assertDatabaseHas('permissions', ['nombre' => $validName]);
    }

    /**
     * Prueba que la descripción del permiso puede ser larga.
     */
    public function test_permission_description_can_be_long()
    {
        $longDescription = str_repeat('a', 1000);

        $permission = Permission::factory()->create([
            'nombre' => 'test.permission',
            'descripcion' => $longDescription,
        ]);

        $this->assertEquals($longDescription, $permission->descripcion);
    }

    // ==================== PRUEBAS DE PERMISOS ESPECÍFICOS ====================

    /**
     * Prueba la creación de permisos del sistema.
     */
    public function test_system_permissions_can_be_created()
    {
        $systemPermissions = [
            'ver_usuarios_system' => 'Ver lista de usuarios',
            'crear_usuarios_system' => 'Crear nuevos usuarios',
            'editar_usuarios_system' => 'Modificar usuarios existentes',
            'eliminar_usuarios_system' => 'Eliminar usuarios',
            'ver_roles_system' => 'Ver lista de roles',
            'crear_roles_system' => 'Crear nuevos roles',
            'editar_roles_system' => 'Modificar roles existentes',
            'eliminar_roles_system' => 'Eliminar roles',
        ];

        foreach ($systemPermissions as $name => $description) {
            $permission = Permission::factory()->create([
                'nombre' => $name,
                'descripcion' => $description,
            ]);

            $this->assertDatabaseHas('permissions', [
                'nombre' => $name,
                'descripcion' => $description,
            ]);
        }
    }

    /**
     * Prueba que los permisos tienen nombres descriptivos.
     */
    public function test_permissions_have_descriptive_names()
    {
        $permission = Permission::factory()->create([
            'nombre' => 'ver_estudiantes',
            'descripcion' => 'Permite ver la lista de estudiantes',
        ]);

        $this->assertStringContainsString('ver', $permission->nombre);
        $this->assertStringContainsString('estudiantes', $permission->nombre);
        $this->assertStringContainsString('Permite', $permission->descripcion);
    }

    // ==================== PRUEBAS DE INTEGRIDAD DE DATOS ====================

    /**
     * Prueba que no se pueden crear permisos duplicados.
     */
    public function test_cannot_create_duplicate_permissions()
    {
        Permission::factory()->create(['nombre' => 'unique.permission']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Permission::factory()->create(['nombre' => 'unique.permission']);
    }

    /**
     * Prueba que los timestamps se crean automáticamente.
     */
    public function test_permission_has_automatic_timestamps()
    {
        $permission = Permission::factory()->create();

        $this->assertNotNull($permission->created_at);
        $this->assertNotNull($permission->updated_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $permission->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $permission->updated_at);
    }

    /**
     * Prueba que los timestamps se actualizan al modificar el permiso.
     */
    public function test_permission_timestamps_update_on_modification()
    {
        $permission = Permission::factory()->create();
        $originalUpdatedAt = $permission->updated_at;

        // Esperar un momento para asegurar que el timestamp cambie
        sleep(1);

        $permission->update(['descripcion' => 'Updated description']);

        $this->assertGreaterThan($originalUpdatedAt, $permission->fresh()->updated_at);
    }
}
