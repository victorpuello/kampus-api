<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Asignatura;
use App\Models\Institucion;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsignaturaControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $institucion;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear institución
        $this->institucion = Institucion::factory()->create();

        // Crear permisos necesarios
        $permissions = [
            'ver_asignaturas',
            'crear_asignaturas',
            'editar_asignaturas',
            'eliminar_asignaturas',
        ];

        foreach ($permissions as $permissionName) {
            Permission::factory()->create(['nombre' => $permissionName]);
        }

        // Crear rol y asignar permisos
        $role = Role::factory()->create(['nombre' => 'admin']);
        $role->permissions()->attach(
            Permission::whereIn('nombre', $permissions)->pluck('id')
        );

        // Crear usuario con la institución y asignar rol
        $this->user = User::factory()->create([
            'institucion_id' => $this->institucion->id,
        ]);
        $this->user->roles()->attach($role->id);
    }

    public function test_can_list_asignaturas()
    {
        // Crear áreas para la institución del usuario
        $areas = Area::factory()->count(3)->create([
            'institucion_id' => $this->institucion->id,
        ]);

        // Crear asignaturas para esas áreas
        foreach ($areas as $area) {
            Asignatura::factory()->create(['area_id' => $area->id]);
        }

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/asignaturas');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_asignatura()
    {
        $area = Area::factory()->create([
            'institucion_id' => $this->institucion->id,
        ]);

        $asignaturaData = [
            'nombre' => 'Nueva Asignatura',
            'porcentaje_area' => 50.00,
            'area_id' => $area->id,
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/v1/asignaturas', $asignaturaData);

        $response->assertStatus(201)
            ->assertJsonFragment(['nombre' => 'Nueva Asignatura']);

        $this->assertDatabaseHas('asignaturas', ['nombre' => 'Nueva Asignatura']);
    }

    public function test_can_show_asignatura()
    {
        $area = Area::factory()->create([
            'institucion_id' => $this->institucion->id,
        ]);

        $asignatura = Asignatura::factory()->create([
            'area_id' => $area->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/asignaturas/'.$asignatura->id);

        $response->assertStatus(200)
            ->assertJsonFragment(['nombre' => $asignatura->nombre]);
    }

    public function test_can_update_asignatura()
    {
        $area = Area::factory()->create([
            'institucion_id' => $this->institucion->id,
        ]);

        $asignatura = Asignatura::factory()->create([
            'area_id' => $area->id,
        ]);

        $updatedData = [
            'nombre' => 'Asignatura Actualizada',
            'porcentaje_area' => 75.00,
        ];

        $response = $this->actingAs($this->user, 'sanctum')->putJson('/api/v1/asignaturas/'.$asignatura->id, $updatedData);

        $response->assertStatus(200)
            ->assertJsonFragment(['nombre' => 'Asignatura Actualizada']);

        $this->assertDatabaseHas('asignaturas', ['id' => $asignatura->id, 'nombre' => 'Asignatura Actualizada', 'porcentaje_area' => 75.00]);
    }

    public function test_can_delete_asignatura()
    {
        $area = Area::factory()->create([
            'institucion_id' => $this->institucion->id,
        ]);

        $asignatura = Asignatura::factory()->create([
            'area_id' => $area->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->deleteJson('/api/v1/asignaturas/'.$asignatura->id);

        $response->assertStatus(204);

        $this->assertSoftDeleted('asignaturas', ['id' => $asignatura->id]);
    }
}
