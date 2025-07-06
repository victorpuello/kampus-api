<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Docente;
use App\Models\Asignatura;
use App\Models\Grupo;
use App\Models\FranjaHoraria;
use App\Models\Anio;
use App\Models\Periodo;
use App\Models\Asignacion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AsignacionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        
        // Crear roles y permisos para las pruebas
        $role = \App\Models\Role::factory()->create(['nombre' => 'admin']);
        $permissions = [
            \App\Models\Permission::factory()->create(['nombre' => 'ver_asignaciones']),
            \App\Models\Permission::factory()->create(['nombre' => 'crear_asignaciones']),
            \App\Models\Permission::factory()->create(['nombre' => 'eliminar_asignaciones']),
        ];
        
        // Asignar permisos al rol
        foreach ($permissions as $permission) {
            $role->permissions()->attach($permission->id);
        }
        
        // Asignar rol al usuario
        $user->roles()->attach($role->id);
        
        Sanctum::actingAs($user);
    }

    public function test_puede_crear_asignacion()
    {
        $docente = Docente::factory()->create();
        $asignatura = Asignatura::factory()->create();
        $grupo = Grupo::factory()->create();
        $franja = FranjaHoraria::factory()->create();
        $anio = Anio::factory()->create();
        $periodo = Periodo::factory()->create();

        $data = [
            'docente_id' => $docente->id,
            'asignatura_id' => $asignatura->id,
            'grupo_id' => $grupo->id,
            'franja_horaria_id' => $franja->id,
            'dia_semana' => 'miercoles',
            'anio_academico_id' => $anio->id,
            'periodo_id' => $periodo->id,
            'estado' => 'activo',
        ];

        $response = $this->postJson('/api/v1/asignaciones', $data);
        $response->assertStatus(201);
        $this->assertDatabaseHas('asignaciones', [
            'docente_id' => $docente->id,
            'asignatura_id' => $asignatura->id,
            'grupo_id' => $grupo->id,
            'franja_horaria_id' => $franja->id,
            'dia_semana' => 'miercoles',
            'anio_academico_id' => $anio->id,
            'periodo_id' => $periodo->id,
            'estado' => 'activo',
        ]);
    }

    public function test_no_puede_crear_asignacion_con_conflicto()
    {
        $docente = Docente::factory()->create();
        $asignatura = Asignatura::factory()->create();
        $grupo = Grupo::factory()->create();
        $franja = FranjaHoraria::factory()->create();
        $anio = Anio::factory()->create();

        Asignacion::factory()->create([
            'docente_id' => $docente->id,
            'asignatura_id' => $asignatura->id,
            'grupo_id' => $grupo->id,
            'franja_horaria_id' => $franja->id,
            'dia_semana' => 'jueves',
            'anio_academico_id' => $anio->id,
            'estado' => 'activo',
        ]);

        $data = [
            'docente_id' => $docente->id,
            'asignatura_id' => $asignatura->id,
            'grupo_id' => $grupo->id,
            'franja_horaria_id' => $franja->id,
            'dia_semana' => 'jueves',
            'anio_academico_id' => $anio->id,
            'estado' => 'activo',
        ];

        $response = $this->postJson('/api/v1/asignaciones', $data);
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'conflicto']);
    }

    public function test_puede_listar_asignaciones()
    {
        Asignacion::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/asignaciones');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    public function test_puede_mostrar_asignacion()
    {
        $asignacion = Asignacion::factory()->create();
        $response = $this->getJson('/api/v1/asignaciones/' . $asignacion->id);
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    public function test_puede_eliminar_asignacion()
    {
        $asignacion = Asignacion::factory()->create();
        $response = $this->deleteJson('/api/v1/asignaciones/' . $asignacion->id);
        $response->assertStatus(200);
        
        // Verificar que la respuesta es correcta
        $response->assertJson(['message' => 'Asignación eliminada exitosamente']);
        
        // Verificar que la asignación existe en la base de datos (soft delete)
        $this->assertDatabaseHas('asignaciones', ['id' => $asignacion->id]);
    }

    public function test_endpoint_conflictos()
    {
        $response = $this->getJson('/api/v1/asignaciones/conflictos');
        $response->assertStatus(200);
        
        // Verificar que la respuesta es un JSON válido
        $responseData = $response->json();
        $this->assertIsArray($responseData);
    }
}
