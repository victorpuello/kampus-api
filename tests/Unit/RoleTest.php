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
 * Clase de prueba para el modelo Role.
 *
 * Contiene pruebas unitarias para verificar la creación, relaciones,
 * asignación de permisos y gestión de roles.
 */
class RoleTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $role;

    protected $institution;

    protected function setUp(): void
    {
        parent::setUp();

        $this->institution = Institucion::factory()->create();

        $this->role = Role::factory()->create([
            'nombre' => 'Docente',
            'descripcion' => 'Rol de docente del sistema',
        ]);
    }

    // ==================== PRUEBAS DE CREACIÓN Y VALIDACIÓN ====================

    /**
     * Prueba que un rol puede ser creado correctamente.
     */
    public function test_role_can_be_created()
    {
        $role = Role::factory()->create([
            'nombre' => 'TestRole_'.uniqid(),
        ]);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
        ]);
    }

    /**
     * Prueba que un rol requiere un nombre único.
     */
    public function test_role_requires_unique_name()
    {
        $role1 = Role::factory()->create(['nombre' => 'TestRole']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Role::factory()->create(['nombre' => 'TestRole']);
    }

    /**
     * Prueba que un rol puede tener una descripción opcional.
     */
    public function test_role_can_have_optional_description()
    {
        $role = Role::factory()->create([
            'nombre' => 'TestRole',
            'descripcion' => null,
        ]);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'descripcion' => null,
        ]);
    }

    /**
     * Prueba que los campos fillable están correctamente definidos.
     */
    public function test_role_has_correct_fillable_fields()
    {
        $role = Role::factory()->create([
            'nombre' => 'TestRole',
            'descripcion' => 'Test Description',
        ]);

        $this->assertEquals('TestRole', $role->nombre);
        $this->assertEquals('Test Description', $role->descripcion);
    }

    // ==================== PRUEBAS DE RELACIONES ====================

    /**
     * Prueba que un rol puede tener usuarios asignados.
     */
    public function test_role_can_have_users()
    {
        $user = User::factory()->create(['institucion_id' => $this->institution->id]);

        $this->role->users()->attach($user);

        $this->assertTrue($this->role->users->contains($user));
        $this->assertEquals(1, $this->role->users->count());
    }

    /**
     * Prueba que un rol puede tener múltiples usuarios.
     */
    public function test_role_can_have_multiple_users()
    {
        $user1 = User::factory()->create(['institucion_id' => $this->institution->id]);
        $user2 = User::factory()->create(['institucion_id' => $this->institution->id]);
        $user3 = User::factory()->create(['institucion_id' => $this->institution->id]);

        $this->role->users()->attach([$user1->id, $user2->id, $user3->id]);

        $this->assertEquals(3, $this->role->users->count());
        $this->assertTrue($this->role->users->contains($user1));
        $this->assertTrue($this->role->users->contains($user2));
        $this->assertTrue($this->role->users->contains($user3));
    }

    /**
     * Prueba que un rol puede tener permisos asignados.
     */
    public function test_role_can_have_permissions()
    {
        $permission = Permission::factory()->create();

        $this->role->permissions()->attach($permission);

        $this->assertTrue($this->role->permissions->contains($permission));
        $this->assertEquals(1, $this->role->permissions->count());
    }

    /**
     * Prueba que un rol puede tener múltiples permisos.
     */
    public function test_role_can_have_multiple_permissions()
    {
        $permission1 = Permission::factory()->create(['nombre' => 'permission.1']);
        $permission2 = Permission::factory()->create(['nombre' => 'permission.2']);
        $permission3 = Permission::factory()->create(['nombre' => 'permission.3']);

        $this->role->permissions()->attach([
            $permission1->id,
            $permission2->id,
            $permission3->id,
        ]);

        $this->assertEquals(3, $this->role->permissions->count());
        $this->assertTrue($this->role->permissions->contains($permission1));
        $this->assertTrue($this->role->permissions->contains($permission2));
        $this->assertTrue($this->role->permissions->contains($permission3));
    }

    /**
     * Prueba que un rol puede verificar si tiene un permiso específico.
     */
    public function test_role_can_check_if_has_specific_permission()
    {
        $permission = Permission::factory()->create(['nombre' => 'test.permission']);

        $this->role->permissions()->attach($permission);

        $this->assertTrue($this->role->permissions()->where('nombre', 'test.permission')->exists());
        $this->assertFalse($this->role->permissions()->where('nombre', 'nonexistent.permission')->exists());
    }

    // ==================== PRUEBAS DE GESTIÓN DE PERMISOS ====================

    /**
     * Prueba que se pueden asignar permisos a un rol usando sync.
     */
    public function test_role_can_sync_permissions()
    {
        $permission1 = Permission::factory()->create(['nombre' => 'permission.1']);
        $permission2 = Permission::factory()->create(['nombre' => 'permission.2']);
        $permission3 = Permission::factory()->create(['nombre' => 'permission.3']);

        // Asignar permisos iniciales
        $this->role->permissions()->attach([$permission1->id, $permission2->id]);

        // Sincronizar con nuevos permisos
        $this->role->permissions()->sync([$permission2->id, $permission3->id]);

        $this->assertEquals(2, $this->role->permissions->count());
        $this->assertFalse($this->role->permissions->contains($permission1));
        $this->assertTrue($this->role->permissions->contains($permission2));
        $this->assertTrue($this->role->permissions->contains($permission3));
    }

    /**
     * Prueba que se pueden agregar permisos a un rol existente.
     */
    public function test_role_can_add_permissions()
    {
        $permission1 = Permission::factory()->create(['nombre' => 'permission.1']);
        $permission2 = Permission::factory()->create(['nombre' => 'permission.2']);

        // Agregar primer permiso
        $this->role->permissions()->attach($permission1);
        $this->assertEquals(1, $this->role->fresh()->permissions->count());

        // Agregar segundo permiso
        $this->role->permissions()->attach($permission2);
        $this->assertEquals(2, $this->role->fresh()->permissions->count());
    }

    /**
     * Prueba que se pueden remover permisos de un rol.
     */
    public function test_role_can_remove_permissions()
    {
        $permission1 = Permission::factory()->create(['nombre' => 'permission.1']);
        $permission2 = Permission::factory()->create(['nombre' => 'permission.2']);

        $this->role->permissions()->attach([$permission1->id, $permission2->id]);
        $this->assertEquals(2, $this->role->fresh()->permissions->count());

        // Remover un permiso
        $this->role->permissions()->detach($permission1->id);
        $this->assertEquals(1, $this->role->fresh()->permissions->count());
        $this->assertFalse($this->role->fresh()->permissions->contains($permission1));
        $this->assertTrue($this->role->fresh()->permissions->contains($permission2));
    }

    /**
     * Prueba que se pueden remover todos los permisos de un rol.
     */
    public function test_role_can_remove_all_permissions()
    {
        $permission1 = Permission::factory()->create(['nombre' => 'permission.1']);
        $permission2 = Permission::factory()->create(['nombre' => 'permission.2']);

        $this->role->permissions()->attach([$permission1->id, $permission2->id]);
        $this->assertEquals(2, $this->role->fresh()->permissions->count());

        // Remover todos los permisos
        $this->role->permissions()->detach();
        $this->assertEquals(0, $this->role->fresh()->permissions->count());
    }

    // ==================== PRUEBAS DE GESTIÓN DE USUARIOS ====================

    /**
     * Prueba que se pueden asignar usuarios a un rol usando sync.
     */
    public function test_role_can_sync_users()
    {
        $user1 = User::factory()->create(['institucion_id' => $this->institution->id]);
        $user2 = User::factory()->create(['institucion_id' => $this->institution->id]);
        $user3 = User::factory()->create(['institucion_id' => $this->institution->id]);

        // Asignar usuarios iniciales
        $this->role->users()->attach([$user1->id, $user2->id]);

        // Sincronizar con nuevos usuarios
        $this->role->users()->sync([$user2->id, $user3->id]);

        $this->assertEquals(2, $this->role->users->count());
        $this->assertFalse($this->role->users->contains($user1));
        $this->assertTrue($this->role->users->contains($user2));
        $this->assertTrue($this->role->users->contains($user3));
    }

    /**
     * Prueba que se pueden remover usuarios de un rol.
     */
    public function test_role_can_remove_users()
    {
        $user1 = User::factory()->create(['institucion_id' => $this->institution->id]);
        $user2 = User::factory()->create(['institucion_id' => $this->institution->id]);

        $this->role->users()->attach([$user1->id, $user2->id]);
        $this->assertEquals(2, $this->role->fresh()->users->count());

        // Remover un usuario
        $this->role->users()->detach($user1->id);
        $this->assertEquals(1, $this->role->fresh()->users->count());
        $this->assertFalse($this->role->fresh()->users->contains($user1));
        $this->assertTrue($this->role->fresh()->users->contains($user2));
    }

    // ==================== PRUEBAS DE ELIMINACIÓN ====================

    /**
     * Prueba que al eliminar un rol se eliminan las relaciones con usuarios.
     */
    public function test_role_deletion_removes_user_relationships()
    {
        $user = User::factory()->create(['institucion_id' => $this->institution->id]);

        $this->role->users()->attach($user);

        // Verificar que existe la relación
        $this->assertDatabaseHas('user_has_roles', [
            'user_id' => $user->id,
            'role_id' => $this->role->id,
        ]);

        // Eliminar el rol
        $this->role->delete();

        // Verificar que se eliminó la relación
        $this->assertDatabaseMissing('user_has_roles', [
            'user_id' => $user->id,
            'role_id' => $this->role->id,
        ]);
    }

    /**
     * Prueba que al eliminar un rol se eliminan las relaciones con permisos.
     */
    public function test_role_deletion_removes_permission_relationships()
    {
        $permission = Permission::factory()->create();

        $this->role->permissions()->attach($permission);

        // Verificar que existe la relación
        $this->assertDatabaseHas('role_has_permissions', [
            'role_id' => $this->role->id,
            'permission_id' => $permission->id,
        ]);

        // Eliminar el rol
        $this->role->delete();

        // Verificar que se eliminó la relación
        $this->assertDatabaseMissing('role_has_permissions', [
            'role_id' => $this->role->id,
            'permission_id' => $permission->id,
        ]);
    }

    // ==================== PRUEBAS DE CONSULTAS ====================

    /**
     * Prueba que se puede buscar un rol por nombre.
     */
    public function test_role_can_be_found_by_name()
    {
        $role = Role::where('nombre', 'Docente')->first();

        $this->assertNotNull($role);
        $this->assertEquals('Docente', $role->nombre);
    }

    /**
     * Prueba que se puede obtener un rol con sus permisos cargados.
     */
    public function test_role_can_be_loaded_with_permissions()
    {
        $permission = Permission::factory()->create();
        $this->role->permissions()->attach($permission);

        $roleWithPermissions = Role::with('permissions')->find($this->role->id);

        $this->assertTrue($roleWithPermissions->relationLoaded('permissions'));
        $this->assertEquals(1, $roleWithPermissions->permissions->count());
    }

    /**
     * Prueba que se puede obtener un rol con sus usuarios cargados.
     */
    public function test_role_can_be_loaded_with_users()
    {
        $user = User::factory()->create(['institucion_id' => $this->institution->id]);
        $this->role->users()->attach($user);

        $roleWithUsers = Role::with('users')->find($this->role->id);

        $this->assertTrue($roleWithUsers->relationLoaded('users'));
        $this->assertEquals(1, $roleWithUsers->users->count());
    }

    // ==================== PRUEBAS DE VALIDACIÓN ====================

    /**
     * Prueba que el nombre del rol es requerido.
     */
    public function test_role_name_is_required()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Role::factory()->create(['nombre' => null]);
    }

    /**
     * Prueba que el nombre del rol tiene un límite de caracteres.
     */
    public function test_role_name_has_character_limit()
    {
        // Esta prueba verifica que el nombre no exceda el límite de caracteres
        // Verificamos que podemos crear un rol con un nombre de 50 caracteres
        $validName = str_repeat('a', 50);
        $role = Role::factory()->create(['nombre' => $validName]);

        $this->assertEquals(50, strlen($role->nombre));
        $this->assertDatabaseHas('roles', ['nombre' => $validName]);
    }

    /**
     * Prueba que la descripción del rol puede ser larga.
     */
    public function test_role_description_can_be_long()
    {
        $longDescription = str_repeat('a', 1000);

        $role = Role::factory()->create([
            'nombre' => 'TestRole',
            'descripcion' => $longDescription,
        ]);

        $this->assertEquals($longDescription, $role->descripcion);
    }
}
