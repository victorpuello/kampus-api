<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Institucion;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $institucion;
    protected $role;

    protected function setUp(): void
    {
        parent::setUp();
        $this->institucion = Institucion::factory()->create();
        $this->role = Role::factory()->create(['nombre' => 'Administrador']);
        $this->admin = User::factory()->create([
            'institucion_id' => $this->institucion->id,
            'estado' => 'activo',
        ]);
        $this->admin->roles()->attach($this->role);
        // Asignar permiso crear_usuarios al rol Administrador
        $permiso = \App\Models\Permission::create([
            'nombre' => 'crear_usuarios',
            'descripcion' => 'Crear nuevos usuarios',
        ]);
        $this->role->permissions()->attach($permiso);
        $this->role->refresh();
        $this->admin->refresh();
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
    }

    public function test_can_create_user_with_valid_data()
    {
        $userData = [
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'email' => 'juan.perez@example.com',
            'username' => 'juanperez',
            'password' => '12345678',
            'institucion_id' => $this->institucion->id,
            'estado' => 'activo',
            'roles' => [$this->role->id],
        ];

        $response = $this->postJson('/api/v1/users', $userData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'data' => [
                         'id', 'nombre', 'apellido', 'email', 'username', 'estado', 'institucion', 'roles'
                     ]
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'juan.perez@example.com',
            'nombre' => 'Juan',
        ]);
    }

    public function test_cannot_create_user_with_duplicate_email()
    {
        $existing = User::factory()->create(['email' => 'repetido@example.com']);
        $userData = [
            'nombre' => 'Ana',
            'apellido' => 'Gómez',
            'email' => 'repetido@example.com',
            'username' => 'anagomez',
            'password' => '12345678',
            'institucion_id' => $this->institucion->id,
            'estado' => 'activo',
            'roles' => [$this->role->id],
        ];
        $response = $this->postJson('/api/v1/users', $userData);
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    public function test_cannot_create_user_with_invalid_email()
    {
        $userData = [
            'nombre' => 'Ana',
            'apellido' => 'Gómez',
            'email' => 'no-es-email',
            'username' => 'anagomez',
            'password' => '12345678',
            'institucion_id' => $this->institucion->id,
            'estado' => 'activo',
            'roles' => [$this->role->id],
        ];
        $response = $this->postJson('/api/v1/users', $userData);
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    public function test_cannot_create_user_with_short_password()
    {
        $userData = [
            'nombre' => 'Ana',
            'apellido' => 'Gómez',
            'email' => 'ana.gomez@example.com',
            'username' => 'anagomez',
            'password' => '123',
            'institucion_id' => $this->institucion->id,
            'estado' => 'activo',
            'roles' => [$this->role->id],
        ];
        $response = $this->postJson('/api/v1/users', $userData);
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['password']);
    }

    public function test_cannot_create_user_without_roles()
    {
        $userData = [
            'nombre' => 'Ana',
            'apellido' => 'Gómez',
            'email' => 'ana.gomez@example.com',
            'username' => 'anagomez',
            'password' => '12345678',
            'institucion_id' => $this->institucion->id,
            'estado' => 'activo',
            'roles' => [],
        ];
        $response = $this->postJson('/api/v1/users', $userData);
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['roles']);
    }

    public function test_cannot_create_user_with_nonexistent_role()
    {
        $userData = [
            'nombre' => 'Ana',
            'apellido' => 'Gómez',
            'email' => 'ana.gomez@example.com',
            'username' => 'anagomez',
            'password' => '12345678',
            'institucion_id' => $this->institucion->id,
            'estado' => 'activo',
            'roles' => [9999],
        ];
        $response = $this->postJson('/api/v1/users', $userData);
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['roles.0']);
    }

    public function test_cannot_create_user_with_duplicate_username()
    {
        $existing = User::factory()->create(['username' => 'duplicado']);
        $userData = [
            'nombre' => 'Ana',
            'apellido' => 'Gómez',
            'email' => 'ana.gomez2@example.com',
            'username' => 'duplicado',
            'password' => '12345678',
            'institucion_id' => $this->institucion->id,
            'estado' => 'activo',
            'roles' => [$this->role->id],
        ];
        $response = $this->postJson('/api/v1/users', $userData);
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['username']);
    }
} 