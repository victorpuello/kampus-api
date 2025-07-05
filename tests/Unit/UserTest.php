<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Institucion;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Clase de prueba para el modelo User.
 *
 * Contiene pruebas unitarias para verificar la creación, relaciones, autenticación
 * y borrado lógico del modelo User.
 */
class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $institution;
    protected $adminRole;
    protected $adminPermission;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear institución
        $this->institution = Institucion::factory()->create();
        
        // Crear rol de administrador
        $this->adminRole = Role::factory()->create([
            'nombre' => 'Administrador',
            'descripcion' => 'Rol de administrador del sistema'
        ]);
        
        // Crear permiso de administrador
        $this->adminPermission = Permission::factory()->create([
            'nombre' => 'admin.access',
            'descripcion' => 'Acceso administrativo'
        ]);
        
        // Asignar permiso al rol
        $this->adminRole->permissions()->attach($this->adminPermission);
        
        // Crear usuario administrador
        $this->user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => '123456',
            'institucion_id' => $this->institution->id,
            'estado' => 'activo'
        ]);
        
        // Asignar rol al usuario
        $this->user->roles()->attach($this->adminRole);
    }

    // ==================== PRUEBAS DE CREACIÓN Y VALIDACIÓN ====================

    /**
     * Prueba que un usuario puede ser creado correctamente.
     */
    public function test_user_can_be_created()
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
    }

    /**
     * Prueba que la contraseña del usuario se hashea automáticamente al crearse.
     */
    public function test_user_password_is_hashed_on_creation()
    {
        $password = 'secretpassword';
        $user = User::factory()->create([
            'password' => $password,
        ]);

        $this->assertTrue(Hash::check($password, $user->password));
    }

    /**
     * Prueba que un usuario puede ser autenticado con la contraseña hasheada.
     */
    public function test_user_can_be_authenticated_with_hashed_password()
    {
        $password = 'secretpassword';
        $user = User::factory()->create([
            'password' => $password,
        ]);

        $this->assertTrue(Hash::check($password, $user->password));
        $this->assertTrue(auth()->attempt(['email' => $user->email, 'password' => $password]));
    }

    // ==================== PRUEBAS DE RELACIONES ====================

    /**
     * Prueba que un usuario pertenece a una institución.
     */
    public function test_user_belongs_to_institucion()
    {
        $institucion = Institucion::factory()->create();
        $user = User::factory()->create(['institucion_id' => $institucion->id]);

        $this->assertEquals($institucion->id, $user->institucion->id);
    }

    /**
     * Prueba que un usuario puede tener roles.
     */
    public function test_user_can_have_roles()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create();

        $user->roles()->attach($role);

        $this->assertTrue($user->roles->contains($role));
    }

    /**
     * Prueba que un usuario puede tener permisos a través de sus roles.
     */
    public function test_user_can_have_permissions_through_roles()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create();
        $permission = Permission::factory()->create();

        $role->permissions()->attach($permission);
        $user->roles()->attach($role);

        $this->assertTrue($user->fresh()->hasPermissionTo($permission->nombre));
    }

    // ==================== PRUEBAS DE TOKENS Y AUTENTICACIÓN ====================

    public function test_user_can_create_token()
    {
        $token = $this->user->createToken('test-token');

        $this->assertInstanceOf(PersonalAccessToken::class, $token);
        $this->assertEquals($this->user->id, $token->tokenable_id);
        $this->assertEquals('test-token', $token->name);
    }

    public function test_user_can_create_plain_text_token()
    {
        $tokenString = $this->user->createToken('test-token')->plainTextToken;

        $this->assertIsString($tokenString);
        $this->assertNotEmpty($tokenString);
        
        // Verificar que el token se guardó en la base de datos
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $this->user->id,
            'name' => 'test-token'
        ]);
    }

    public function test_user_can_revoke_all_tokens()
    {
        // Crear múltiples tokens
        $this->user->createToken('token-1');
        $this->user->createToken('token-2');
        $this->user->createToken('token-3');

        // Verificar que existen
        $this->assertDatabaseCount('personal_access_tokens', 3);

        // Revocar todos los tokens
        $this->user->tokens()->delete();

        // Verificar que fueron eliminados
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_user_can_revoke_specific_token()
    {
        $token = $this->user->createToken('test-token');

        // Verificar que existe
        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $token->id
        ]);

        // Revocar el token específico
        $token->delete();

        // Verificar que fue eliminado
        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->id
        ]);
    }

    public function test_user_has_roles_relationship()
    {
        $this->assertTrue($this->user->roles->contains($this->adminRole));
        $this->assertEquals(1, $this->user->roles->count());
    }

    public function test_user_can_have_multiple_roles()
    {
        // Crear rol adicional
        $teacherRole = Role::factory()->create([
            'nombre' => 'Docente',
            'descripcion' => 'Rol de docente'
        ]);

        // Asignar rol adicional
        $this->user->roles()->attach($teacherRole);

        // Verificar que tiene ambos roles
        $this->assertEquals(2, $this->user->roles->count());
        $this->assertTrue($this->user->roles->contains($this->adminRole));
        $this->assertTrue($this->user->roles->contains($teacherRole));
    }

    public function test_user_can_check_permissions()
    {
        // Verificar que tiene el permiso correcto
        $this->assertTrue($this->user->hasPermissionTo('admin.access'));
        
        // Verificar que no tiene un permiso que no tiene
        $this->assertFalse($this->user->hasPermissionTo('nonexistent.permission'));
    }

    public function test_user_has_permissions_through_roles()
    {
        // Crear permiso adicional
        $teacherPermission = Permission::factory()->create([
            'nombre' => 'teacher.access',
            'descripcion' => 'Acceso de docente'
        ]);

        // Crear rol de docente
        $teacherRole = Role::factory()->create([
            'nombre' => 'Docente',
            'descripcion' => 'Rol de docente'
        ]);

        // Asignar permiso al rol de docente
        $teacherRole->permissions()->attach($teacherPermission);

        // Asignar rol de docente al usuario
        $this->user->roles()->attach($teacherRole);

        // Verificar que tiene ambos permisos
        $this->assertTrue($this->user->hasPermissionTo('admin.access'));
        $this->assertTrue($this->user->hasPermissionTo('teacher.access'));
    }

    public function test_user_has_institution_relationship()
    {
        $this->assertInstanceOf(Institucion::class, $this->user->institucion);
        $this->assertEquals($this->institution->id, $this->user->institucion->id);
    }

    // ==================== PRUEBAS DE CONTRASEÑAS ====================

    public function test_user_password_is_hashed_when_set()
    {
        $user = User::factory()->create([
            'password' => 'plaintextpassword'
        ]);

        // Verificar que la contraseña fue hasheada
        $this->assertNotEquals('plaintextpassword', $user->password);
        $this->assertTrue(Hash::check('plaintextpassword', $user->password));
    }

    public function test_user_password_is_not_rehashed_when_not_changed()
    {
        $originalPassword = $this->user->password;
        
        // Actualizar sin cambiar la contraseña
        $this->user->update(['nombre' => 'Nuevo Nombre']);
        
        // Verificar que la contraseña no cambió
        $this->assertEquals($originalPassword, $this->user->fresh()->password);
    }

    public function test_user_password_is_hidden_from_arrays()
    {
        $userArray = $this->user->toArray();
        
        $this->assertArrayNotHasKey('password', $userArray);
    }

    // ==================== PRUEBAS DE SOFT DELETE ====================

    public function test_user_can_be_soft_deleted()
    {
        $userId = $this->user->id;
        
        // Soft delete
        $this->user->delete();
        
        // Verificar que está soft deleted
        $this->assertSoftDeleted('users', ['id' => $userId]);
        
        // Verificar que no se puede encontrar normalmente
        $this->assertNull(User::find($userId));
        
        // Verificar que se puede encontrar con withTrashed
        $this->assertNotNull(User::withTrashed()->find($userId));
    }

    public function test_user_can_be_restored()
    {
        $userId = $this->user->id;
        
        // Soft delete
        $this->user->delete();
        
        // Restaurar
        $this->user->restore();
        
        // Verificar que ya no está soft deleted
        $this->assertNotSoftDeleted('users', ['id' => $userId]);
        
        // Verificar que se puede encontrar normalmente
        $this->assertNotNull(User::find($userId));
    }

    public function test_user_can_be_force_deleted()
    {
        $userId = $this->user->id;
        
        // Force delete
        $this->user->forceDelete();
        
        // Verificar que fue eliminado permanentemente
        $this->assertDatabaseMissing('users', ['id' => $userId]);
        $this->assertNull(User::withTrashed()->find($userId));
    }

    // ==================== PRUEBAS DE RELACIONES ESPECÍFICAS ====================

    public function test_user_has_docente_relationship()
    {
        // Crear docente asociado
        $docente = \App\Models\Docente::factory()->create([
            'user_id' => $this->user->id
        ]);

        $this->assertInstanceOf(\App\Models\Docente::class, $this->user->docente);
        $this->assertEquals($docente->id, $this->user->docente->id);
    }

    public function test_user_has_estudiante_relationship()
    {
        // Crear estudiante asociado
        $estudiante = \App\Models\Estudiante::factory()->create([
            'user_id' => $this->user->id
        ]);

        $this->assertInstanceOf(\App\Models\Estudiante::class, $this->user->estudiante);
        $this->assertEquals($estudiante->id, $this->user->estudiante->id);
    }

    public function test_user_has_acudiente_relationship()
    {
        // Crear acudiente asociado
        $acudiente = \App\Models\Acudiente::factory()->create([
            'user_id' => $this->user->id
        ]);

        $this->assertInstanceOf(\App\Models\Acudiente::class, $this->user->acudiente);
        $this->assertEquals($acudiente->id, $this->user->acudiente->id);
    }

    // ==================== PRUEBAS AVANZADAS DE TOKENS ====================

    public function test_user_can_have_multiple_tokens_with_different_names()
    {
        $token1 = $this->user->createToken('mobile-app');
        $token2 = $this->user->createToken('web-app');
        $token3 = $this->user->createToken('api-client');

        $this->assertEquals(3, $this->user->tokens()->count());
        
        // Verificar que cada token tiene un nombre único
        $tokenNames = $this->user->tokens()->pluck('name')->toArray();
        $this->assertCount(3, array_unique($tokenNames));
        $this->assertContains('mobile-app', $tokenNames);
        $this->assertContains('web-app', $tokenNames);
        $this->assertContains('api-client', $tokenNames);
    }

    public function test_user_tokens_are_deleted_when_user_is_deleted()
    {
        // Crear tokens
        $this->user->createToken('token-1');
        $this->user->createToken('token-2');

        // Verificar que existen
        $this->assertDatabaseCount('personal_access_tokens', 2);

        // Eliminar usuario
        $this->user->forceDelete();

        // Verificar que los tokens fueron eliminados
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_user_can_check_multiple_permissions()
    {
        // Crear permisos adicionales
        $permission1 = Permission::factory()->create(['nombre' => 'permission.1']);
        $permission2 = Permission::factory()->create(['nombre' => 'permission.2']);
        $permission3 = Permission::factory()->create(['nombre' => 'permission.3']);

        // Asignar permisos al rol
        $this->adminRole->permissions()->attach([$permission1->id, $permission2->id]);

        // Verificar permisos
        $this->assertTrue($this->user->hasPermissionTo('admin.access'));
        $this->assertTrue($this->user->hasPermissionTo('permission.1'));
        $this->assertTrue($this->user->hasPermissionTo('permission.2'));
        $this->assertFalse($this->user->hasPermissionTo('permission.3'));
    }

    public function test_user_roles_are_deleted_when_user_is_deleted()
    {
        // Verificar que tiene el rol
        $this->assertDatabaseHas('user_has_roles', [
            'user_id' => $this->user->id,
            'role_id' => $this->adminRole->id
        ]);

        // Eliminar usuario
        $this->user->forceDelete();

        // Verificar que la relación fue eliminada
        $this->assertDatabaseMissing('user_has_roles', [
            'user_id' => $this->user->id,
            'role_id' => $this->adminRole->id
        ]);
    }
} 