<?php

namespace Tests\Feature;

use App\Models\Anio;
use App\Models\Docente;
use App\Models\Grado;
use App\Models\Grupo;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GrupoControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $adminRole;

    protected $institucion;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear institución
        $this->institucion = \App\Models\Institucion::factory()->create();

        // Crear rol de administrador
        $this->adminRole = Role::factory()->create([
            'nombre' => 'admin',
            'descripcion' => 'Rol de administrador del sistema',
        ]);

        // Crear permisos específicos para grupos
        $permissions = [
            'ver_grupos',
            'crear_grupos',
            'editar_grupos',
            'eliminar_grupos',
            'matricular_estudiantes',
        ];

        foreach ($permissions as $permissionName) {
            Permission::factory()->create(['nombre' => $permissionName]);
        }

        // Asignar permisos al rol
        $this->adminRole->permissions()->attach(
            Permission::whereIn('nombre', $permissions)->pluck('id')
        );

        // Crear usuario con institución
        $this->user = User::factory()->create([
            'institucion_id' => $this->institucion->id,
        ]);

        // Asignar rol al usuario
        $this->user->roles()->attach($this->adminRole);
    }

    public function test_can_list_grupos()
    {
        // Crear grupos que pertenezcan a la institución del usuario
        $grado = Grado::factory()->create(['institucion_id' => $this->institucion->id]);
        $anio = Anio::factory()->create(['institucion_id' => $this->institucion->id]);
        $sede = \App\Models\Sede::factory()->create(['institucion_id' => $this->institucion->id]);

        Grupo::factory()->count(3)->create([
            'grado_id' => $grado->id,
            'anio_id' => $anio->id,
            'sede_id' => $sede->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/grupos');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_grupo()
    {
        $anio = Anio::factory()->create(['institucion_id' => $this->institucion->id]);
        $grado = Grado::factory()->create(['institucion_id' => $this->institucion->id]);
        $docente = Docente::factory()->create();
        $sede = \App\Models\Sede::factory()->create(['institucion_id' => $this->institucion->id]);

        $grupoData = [
            'nombre' => 'Nuevo Grupo',
            'anio_id' => $anio->id,
            'grado_id' => $grado->id,
            'director_docente_id' => $docente->id,
            'sede_id' => $sede->id,
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/v1/grupos', $grupoData);

        $response->assertStatus(201)
            ->assertJsonFragment(['nombre' => 'Nuevo Grupo']);

        $this->assertDatabaseHas('grupos', ['nombre' => 'Nuevo Grupo']);
    }

    public function test_can_show_grupo()
    {
        $grado = Grado::factory()->create(['institucion_id' => $this->institucion->id]);
        $anio = Anio::factory()->create(['institucion_id' => $this->institucion->id]);
        $sede = \App\Models\Sede::factory()->create(['institucion_id' => $this->institucion->id]);

        $grupo = Grupo::factory()->create([
            'grado_id' => $grado->id,
            'anio_id' => $anio->id,
            'sede_id' => $sede->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/grupos/'.$grupo->id);

        $response->assertStatus(200)
            ->assertJsonFragment(['nombre' => $grupo->nombre]);
    }

    public function test_can_update_grupo()
    {
        $grado = Grado::factory()->create(['institucion_id' => $this->institucion->id]);
        $anio = Anio::factory()->create(['institucion_id' => $this->institucion->id]);
        $sede = \App\Models\Sede::factory()->create(['institucion_id' => $this->institucion->id]);

        $grupo = Grupo::factory()->create([
            'grado_id' => $grado->id,
            'anio_id' => $anio->id,
            'sede_id' => $sede->id,
        ]);

        $updatedData = [
            'nombre' => 'Grupo Actualizado',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->putJson('/api/v1/grupos/'.$grupo->id, $updatedData);

        $response->assertStatus(200)
            ->assertJsonFragment(['nombre' => 'Grupo Actualizado']);

        $this->assertDatabaseHas('grupos', ['id' => $grupo->id, 'nombre' => 'Grupo Actualizado']);
    }

    public function test_can_delete_grupo()
    {
        $grado = Grado::factory()->create(['institucion_id' => $this->institucion->id]);
        $anio = Anio::factory()->create(['institucion_id' => $this->institucion->id]);
        $sede = \App\Models\Sede::factory()->create(['institucion_id' => $this->institucion->id]);

        $grupo = Grupo::factory()->create([
            'grado_id' => $grado->id,
            'anio_id' => $anio->id,
            'sede_id' => $sede->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->deleteJson('/api/v1/grupos/'.$grupo->id);

        $response->assertStatus(204);

        $this->assertSoftDeleted('grupos', ['id' => $grupo->id]);
    }
}
