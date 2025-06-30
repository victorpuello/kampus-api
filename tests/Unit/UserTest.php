<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Institucion;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Clase de prueba para el modelo User.
 *
 * Contiene pruebas unitarias para verificar la creación, relaciones y borrado lógico
 * del modelo User.
 */
class UserTest extends TestCase
{
    use RefreshDatabase;

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

    /**
     * Prueba que un usuario puede ser eliminado lógicamente.
     */
    public function test_user_can_be_soft_deleted()
    {
        $user = User::factory()->create();
        $user->delete();

        $this->assertSoftDeleted($user);
    }
} 