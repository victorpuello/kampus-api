<?php

namespace Tests\Feature;

use App\Models\Asignacion;
use App\Models\Docente;
use App\Models\Asignatura;
use App\Models\Grupo;
use App\Models\Anio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsignacionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_asignaciones()
    {
        Asignacion::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/asignaciones');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_can_create_asignacion()
    {
        $docente = Docente::factory()->create();
        $asignatura = Asignatura::factory()->create();
        $grupo = Grupo::factory()->create();
        $anio = Anio::factory()->create();

        $asignacionData = [
            'docente_id' => $docente->id,
            'asignatura_id' => $asignatura->id,
            'grupo_id' => $grupo->id,
            'anio_id' => $anio->id,
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/v1/asignaciones', $asignacionData);

        $response->assertStatus(201)
                 ->assertJsonFragment(['docente_id' => $docente->id]);

        $this->assertDatabaseHas('asignaciones', ['docente_id' => $docente->id]);
    }

    public function test_can_show_asignacion()
    {
        $asignacion = Asignacion::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/asignaciones/' . $asignacion->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['docente_id' => $asignacion->docente_id]);
    }

    public function test_can_update_asignacion()
    {
        $asignacion = Asignacion::factory()->create();
        $newDocente = Docente::factory()->create();
        $updatedData = [
            'docente_id' => $newDocente->id,
        ];

        $response = $this->actingAs($this->user, 'sanctum')->putJson('/api/v1/asignaciones/' . $asignacion->id, $updatedData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['docente_id' => $newDocente->id]);

        $this->assertDatabaseHas('asignaciones', ['id' => $asignacion->id, 'docente_id' => $newDocente->id]);
    }

    public function test_can_delete_asignacion()
    {
        $asignacion = Asignacion::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')->deleteJson('/api/v1/asignaciones/' . $asignacion->id);

        $response->assertStatus(204);

        $this->assertSoftDeleted('asignaciones', ['id' => $asignacion->id]);
    }
}
