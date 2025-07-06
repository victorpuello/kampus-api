<?php

namespace Tests\Feature;

use App\Models\Institucion;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Clase de prueba de integración para el sistema de permisos y roles.
 *
 * Contiene pruebas que verifican la interacción completa entre usuarios,
 * roles y permisos, así como el funcionamiento del sistema de autorización.
 */
class RolePermissionSystemTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $institution;

    protected $adminUser;

    protected $teacherUser;

    protected $studentUser;

    protected $adminRole;

    protected $teacherRole;

    protected $studentRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->institution = Institucion::factory()->create();

        // Crear roles del sistema
        $this->adminRole = Role::factory()->create([
            'nombre' => 'Administrador',
            'descripcion' => 'Acceso total al sistema',
        ]);

        $this->teacherRole = Role::factory()->create([
            'nombre' => 'Docente',
            'descripcion' => 'Gestiona sus clases y estudiantes',
        ]);

        $this->studentRole = Role::factory()->create([
            'nombre' => 'Estudiante',
            'descripcion' => 'Acceso a sus notas y actividades',
        ]);

        // Crear usuarios
        $this->adminUser = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => '123456',
            'institucion_id' => $this->institution->id,
            'estado' => 'activo',
        ]);

        $this->teacherUser = User::factory()->create([
            'email' => 'teacher@example.com',
            'password' => '123456',
            'institucion_id' => $this->institution->id,
            'estado' => 'activo',
        ]);

        $this->studentUser = User::factory()->create([
            'email' => 'student@example.com',
            'password' => '123456',
            'institucion_id' => $this->institution->id,
            'estado' => 'activo',
        ]);
    }

    // ==================== PRUEBAS DE CONFIGURACIÓN DEL SISTEMA ====================

    /**
     * Prueba la configuración inicial del sistema de permisos y roles.
     */
    public function test_system_initial_setup()
    {
        // Verificar que los roles existen
        $this->assertDatabaseHas('roles', ['nombre' => 'Administrador']);
        $this->assertDatabaseHas('roles', ['nombre' => 'Docente']);
        $this->assertDatabaseHas('roles', ['nombre' => 'Estudiante']);

        // Verificar que los usuarios existen
        $this->assertDatabaseHas('users', ['email' => 'admin@example.com']);
        $this->assertDatabaseHas('users', ['email' => 'teacher@example.com']);
        $this->assertDatabaseHas('users', ['email' => 'student@example.com']);
    }

    /**
     * Prueba la creación y asignación de permisos del sistema.
     */
    public function test_system_permissions_creation_and_assignment()
    {
        // Crear permisos del sistema
        $permissions = [
            'ver_usuarios' => 'Ver lista de usuarios',
            'crear_usuarios' => 'Crear nuevos usuarios',
            'editar_usuarios' => 'Modificar usuarios existentes',
            'eliminar_usuarios' => 'Eliminar usuarios',
            'ver_estudiantes' => 'Ver lista de estudiantes',
            'crear_estudiantes' => 'Crear nuevos estudiantes',
            'editar_estudiantes' => 'Modificar estudiantes',
            'ver_notas' => 'Ver notas de estudiantes',
            'crear_notas' => 'Crear nuevas notas',
            'editar_notas' => 'Modificar notas',
        ];

        $createdPermissions = [];
        foreach ($permissions as $name => $description) {
            $permission = Permission::factory()->create([
                'nombre' => $name,
                'descripcion' => $description,
            ]);
            $createdPermissions[$name] = $permission;
        }

        // Asignar permisos al rol de administrador
        $adminPermissions = [
            'ver_usuarios', 'crear_usuarios', 'editar_usuarios', 'eliminar_usuarios',
            'ver_estudiantes', 'crear_estudiantes', 'editar_estudiantes',
            'ver_notas', 'crear_notas', 'editar_notas',
        ];

        $this->adminRole->permissions()->attach(
            collect($createdPermissions)->only($adminPermissions)->pluck('id')->toArray()
        );

        // Asignar permisos al rol de docente
        $teacherPermissions = [
            'ver_estudiantes', 'ver_notas', 'crear_notas', 'editar_notas',
        ];

        $this->teacherRole->permissions()->attach(
            collect($createdPermissions)->only($teacherPermissions)->pluck('id')->toArray()
        );

        // Asignar permisos al rol de estudiante
        $studentPermissions = ['ver_notas'];

        $this->studentRole->permissions()->attach(
            collect($createdPermissions)->only($studentPermissions)->pluck('id')->toArray()
        );

        // Verificar asignaciones
        $this->assertEquals(10, $this->adminRole->permissions->count());
        $this->assertEquals(4, $this->teacherRole->permissions->count());
        $this->assertEquals(1, $this->studentRole->permissions->count());
    }

    // ==================== PRUEBAS DE ASIGNACIÓN DE ROLES ====================

    /**
     * Prueba la asignación de roles a usuarios.
     */
    public function test_role_assignment_to_users()
    {
        // Asignar roles a usuarios
        $this->adminUser->roles()->attach($this->adminRole);
        $this->teacherUser->roles()->attach($this->teacherRole);
        $this->studentUser->roles()->attach($this->studentRole);

        // Verificar asignaciones
        $this->assertTrue($this->adminUser->hasRole('Administrador'));
        $this->assertTrue($this->teacherUser->hasRole('Docente'));
        $this->assertTrue($this->studentUser->hasRole('Estudiante'));

        // Verificar que no tienen roles incorrectos
        $this->assertFalse($this->adminUser->hasRole('Docente'));
        $this->assertFalse($this->teacherUser->hasRole('Administrador'));
        $this->assertFalse($this->studentUser->hasRole('Docente'));
    }

    /**
     * Prueba que un usuario puede tener múltiples roles.
     */
    public function test_user_can_have_multiple_roles()
    {
        // Asignar múltiples roles al usuario administrador
        $this->adminUser->roles()->attach([$this->adminRole->id, $this->teacherRole->id]);

        $this->assertTrue($this->adminUser->hasRole('Administrador'));
        $this->assertTrue($this->adminUser->hasRole('Docente'));
        $this->assertEquals(2, $this->adminUser->roles->count());
    }

    /**
     * Prueba la verificación de roles por ID y nombre.
     */
    public function test_role_verification_by_id_and_name()
    {
        $this->adminUser->roles()->attach($this->adminRole);

        // Verificar por nombre
        $this->assertTrue($this->adminUser->hasRole('Administrador'));

        // Verificar por ID
        $this->assertTrue($this->adminUser->hasRole($this->adminRole->id));

        // Verificar que no tiene un rol inexistente
        $this->assertFalse($this->adminUser->hasRole('RolInexistente'));
        $this->assertFalse($this->adminUser->hasRole(999));
    }

    // ==================== PRUEBAS DE VERIFICACIÓN DE PERMISOS ====================

    /**
     * Prueba la verificación de permisos a través de roles.
     */
    public function test_permission_verification_through_roles()
    {
        // Crear permisos
        $userPermission = Permission::factory()->create(['nombre' => 'ver_usuarios']);
        $studentPermission = Permission::factory()->create(['nombre' => 'ver_estudiantes']);
        $notePermission = Permission::factory()->create(['nombre' => 'ver_notas']);

        // Asignar permisos a roles
        $this->adminRole->permissions()->attach([$userPermission->id, $studentPermission->id, $notePermission->id]);
        $this->teacherRole->permissions()->attach([$studentPermission->id, $notePermission->id]);
        $this->studentRole->permissions()->attach([$notePermission->id]);

        // Asignar roles a usuarios
        $this->adminUser->roles()->attach($this->adminRole);
        $this->teacherUser->roles()->attach($this->teacherRole);
        $this->studentUser->roles()->attach($this->studentRole);

        // Verificar permisos del administrador
        $this->assertTrue($this->adminUser->hasPermissionTo('ver_usuarios'));
        $this->assertTrue($this->adminUser->hasPermissionTo('ver_estudiantes'));
        $this->assertTrue($this->adminUser->hasPermissionTo('ver_notas'));

        // Verificar permisos del docente
        $this->assertFalse($this->teacherUser->hasPermissionTo('ver_usuarios'));
        $this->assertTrue($this->teacherUser->hasPermissionTo('ver_estudiantes'));
        $this->assertTrue($this->teacherUser->hasPermissionTo('ver_notas'));

        // Verificar permisos del estudiante
        $this->assertFalse($this->studentUser->hasPermissionTo('ver_usuarios'));
        $this->assertFalse($this->studentUser->hasPermissionTo('ver_estudiantes'));
        $this->assertTrue($this->studentUser->hasPermissionTo('ver_notas'));
    }

    /**
     * Prueba que un usuario con múltiples roles tiene todos los permisos.
     */
    public function test_user_with_multiple_roles_has_all_permissions()
    {
        // Crear permisos
        $adminPermission = Permission::factory()->create(['nombre' => 'admin.access']);
        $teacherPermission = Permission::factory()->create(['nombre' => 'teacher.access']);

        // Asignar permisos a roles
        $this->adminRole->permissions()->attach($adminPermission);
        $this->teacherRole->permissions()->attach($teacherPermission);

        // Asignar ambos roles al usuario
        $this->adminUser->roles()->attach([$this->adminRole->id, $this->teacherRole->id]);

        // Verificar que tiene todos los permisos
        $this->assertTrue($this->adminUser->hasPermissionTo('admin.access'));
        $this->assertTrue($this->adminUser->hasPermissionTo('teacher.access'));
    }

    /**
     * Prueba el método can() para verificación de permisos.
     */
    public function test_can_method_for_permission_verification()
    {
        // Crear permisos
        $userPermission = Permission::factory()->create(['nombre' => 'crear_usuarios']);
        $this->adminRole->permissions()->attach($userPermission);
        $this->adminUser->roles()->attach($this->adminRole);

        // Verificar usando el método can()
        $this->assertTrue($this->adminUser->can('users.create'));
        $this->assertFalse($this->adminUser->can('nonexistent.permission'));
    }

    // ==================== PRUEBAS DE GESTIÓN DE PERMISOS ====================

    /**
     * Prueba la obtención de todos los permisos de un usuario.
     */
    public function test_get_all_user_permissions()
    {
        // Crear permisos
        $permission1 = Permission::factory()->create(['nombre' => 'permission.1']);
        $permission2 = Permission::factory()->create(['nombre' => 'permission.2']);
        $permission3 = Permission::factory()->create(['nombre' => 'permission.3']);

        // Asignar permisos a roles
        $this->adminRole->permissions()->attach([$permission1->id, $permission2->id]);
        $this->teacherRole->permissions()->attach([$permission2->id, $permission3->id]);

        // Asignar ambos roles al usuario
        $this->adminUser->roles()->attach([$this->adminRole->id, $this->teacherRole->id]);

        // Obtener todos los permisos
        $allPermissions = $this->adminUser->getAllPermissions();

        // Verificar que tiene todos los permisos únicos
        $this->assertEquals(3, $allPermissions->count());
        $this->assertTrue($allPermissions->pluck('nombre')->contains('permission.1'));
        $this->assertTrue($allPermissions->pluck('nombre')->contains('permission.2'));
        $this->assertTrue($allPermissions->pluck('nombre')->contains('permission.3'));
    }

    /**
     * Prueba la sincronización de permisos en roles.
     */
    public function test_permission_sync_on_roles()
    {
        // Crear permisos
        $permission1 = Permission::factory()->create(['nombre' => 'permission.1']);
        $permission2 = Permission::factory()->create(['nombre' => 'permission.2']);
        $permission3 = Permission::factory()->create(['nombre' => 'permission.3']);

        // Asignar permisos iniciales
        $this->adminRole->permissions()->attach([$permission1->id, $permission2->id]);

        // Sincronizar con nuevos permisos
        $this->adminRole->permissions()->sync([$permission2->id, $permission3->id]);

        // Verificar resultado
        $this->assertEquals(2, $this->adminRole->permissions->count());
        $this->assertFalse($this->adminRole->permissions->contains($permission1));
        $this->assertTrue($this->adminRole->permissions->contains($permission2));
        $this->assertTrue($this->adminRole->permissions->contains($permission3));
    }

    // ==================== PRUEBAS DE ESCENARIOS REALES ====================

    /**
     * Prueba un escenario real de gestión de permisos académicos.
     */
    public function test_real_academic_permission_scenario()
    {
        // Crear permisos académicos
        $academicPermissions = [
            'ver_estudiantes' => 'Ver lista de estudiantes',
            'crear_estudiantes' => 'Crear nuevos estudiantes',
            'editar_estudiantes' => 'Modificar estudiantes',
            'ver_notas' => 'Ver notas de estudiantes',
            'crear_notas' => 'Crear nuevas notas',
            'editar_notas' => 'Modificar notas',
            'eliminar_notas' => 'Eliminar notas',
            'ver_reportes' => 'Ver reportes académicos',
            'generar_reportes' => 'Generar reportes académicos',
        ];

        $permissions = [];
        foreach ($academicPermissions as $name => $description) {
            $permissions[$name] = Permission::factory()->create([
                'nombre' => $name,
                'descripcion' => $description,
            ]);
        }

        // Configurar permisos por rol
        $adminPermissionIds = array_keys($academicPermissions);
        $teacherPermissionIds = ['ver_estudiantes', 'ver_notas', 'crear_notas', 'editar_notas', 'ver_reportes'];
        $studentPermissionIds = ['ver_notas'];

        // Asignar permisos a roles
        $this->adminRole->permissions()->attach(
            collect($permissions)->only($adminPermissionIds)->pluck('id')->toArray()
        );
        $this->teacherRole->permissions()->attach(
            collect($permissions)->only($teacherPermissionIds)->pluck('id')->toArray()
        );
        $this->studentRole->permissions()->attach(
            collect($permissions)->only($studentPermissionIds)->pluck('id')->toArray()
        );

        // Asignar roles a usuarios
        $this->adminUser->roles()->attach($this->adminRole);
        $this->teacherUser->roles()->attach($this->teacherRole);
        $this->studentUser->roles()->attach($this->studentRole);

        // Verificar permisos del administrador
        foreach ($academicPermissions as $permission => $description) {
            $this->assertTrue(
                $this->adminUser->hasPermissionTo($permission),
                "El administrador debería tener el permiso: {$permission}"
            );
        }

        // Verificar permisos del docente
        $this->assertTrue($this->teacherUser->hasPermissionTo('ver_estudiantes'));
        $this->assertTrue($this->teacherUser->hasPermissionTo('ver_notas'));
        $this->assertTrue($this->teacherUser->hasPermissionTo('crear_notas'));
        $this->assertFalse($this->teacherUser->hasPermissionTo('crear_estudiantes'));
        $this->assertFalse($this->teacherUser->hasPermissionTo('eliminar_notas'));

        // Verificar permisos del estudiante
        $this->assertTrue($this->studentUser->hasPermissionTo('ver_notas'));
        $this->assertFalse($this->studentUser->hasPermissionTo('ver_estudiantes'));
        $this->assertFalse($this->studentUser->hasPermissionTo('crear_notas'));
    }

    /**
     * Prueba la gestión de permisos dinámicos.
     */
    public function test_dynamic_permission_management()
    {
        // Crear un nuevo permiso dinámicamente
        $newPermission = Permission::factory()->create([
            'nombre' => 'nuevo_permiso',
            'descripcion' => 'Nuevo permiso agregado dinámicamente',
        ]);

        // Asignar el nuevo permiso al rol de administrador
        $this->adminRole->permissions()->attach($newPermission);
        $this->adminUser->roles()->attach($this->adminRole);

        // Verificar que el administrador tiene el nuevo permiso
        $this->assertTrue($this->adminUser->hasPermissionTo('nuevo_permiso'));

        // Remover el permiso del rol
        $this->adminRole->permissions()->detach($newPermission);

        // Verificar que ya no tiene el permiso
        $this->assertFalse($this->adminUser->fresh()->hasPermissionTo('nuevo_permiso'));
    }

    // ==================== PRUEBAS DE INTEGRIDAD DEL SISTEMA ====================

    /**
     * Prueba la integridad del sistema al eliminar roles.
     */
    public function test_system_integrity_when_deleting_roles()
    {
        // Crear permisos y asignar a roles
        $permission = Permission::factory()->create(['nombre' => 'test.permission']);
        $this->adminRole->permissions()->attach($permission);
        $this->adminUser->roles()->attach($this->adminRole);

        // Verificar que el usuario tiene el permiso
        $this->assertTrue($this->adminUser->hasPermissionTo('test.permission'));

        // Eliminar el rol
        $this->adminRole->delete();

        // Verificar que el usuario ya no tiene el permiso
        $this->assertFalse($this->adminUser->fresh()->hasPermissionTo('test.permission'));
        $this->assertEquals(0, $this->adminUser->fresh()->roles->count());
    }

    /**
     * Prueba la integridad del sistema al eliminar permisos.
     */
    public function test_system_integrity_when_deleting_permissions()
    {
        // Crear permisos y asignar a roles
        $permission = Permission::factory()->create(['nombre' => 'test.permission']);
        $this->adminRole->permissions()->attach($permission);
        $this->adminUser->roles()->attach($this->adminRole);

        // Verificar que el usuario tiene el permiso
        $this->assertTrue($this->adminUser->hasPermissionTo('test.permission'));

        // Eliminar el permiso
        $permission->delete();

        // Verificar que el usuario ya no tiene el permiso
        $this->assertFalse($this->adminUser->fresh()->hasPermissionTo('test.permission'));
        $this->assertEquals(0, $this->adminRole->fresh()->permissions->count());
    }

    /**
     * Prueba la integridad del sistema al eliminar usuarios.
     */
    public function test_system_integrity_when_deleting_users()
    {
        // Asignar roles a usuarios
        $this->adminUser->roles()->attach($this->adminRole);
        $this->teacherUser->roles()->attach($this->teacherRole);

        // Verificar que existen las relaciones
        $this->assertDatabaseHas('user_has_roles', [
            'user_id' => $this->adminUser->id,
            'role_id' => $this->adminRole->id,
        ]);

        // Eliminar el usuario administrador con force delete
        $this->adminUser->forceDelete();

        // Verificar que se eliminaron las relaciones
        $this->assertDatabaseMissing('user_has_roles', [
            'user_id' => $this->adminUser->id,
            'role_id' => $this->adminRole->id,
        ]);

        // Verificar que el rol sigue existiendo
        $this->assertDatabaseHas('roles', ['id' => $this->adminRole->id]);
    }
}
