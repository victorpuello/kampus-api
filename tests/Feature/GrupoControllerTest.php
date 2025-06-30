<?php

namespace Tests\Feature;

use App\Models\Grupo;
use App\Models\Anio;
use App\Models\Grado;
use App\Models\Docente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GrupoControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_grupos()
    {
        Grupo::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/grupos');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_can_create_grupo()
    {
        $anio = Anio::factory()->create();
        $grado = Grado::factory()->create();
        $docente = Docente::factory()->create();

        $grupoData = [
            'nombre' => 'Nuevo Grupo',
            'anio_id' => $anio->id,
            'grado_id' => $grado->id,
            'director_docente_id' => $docente->id,
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/v1/grupos', $grupoData);

        $response->assertStatus(201)
                 ->assertJsonFragment(['nombre' => 'Nuevo Grupo']);

        $this->assertDatabaseHas('grupos', ['nombre' => 'Nuevo Grupo']);
    }

    public function test_can_show_grupo()
    {
        $grupo = Grupo::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/grupos/' . $grupo->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['nombre' => $grupo->nombre]);
    }

    public function test_can_update_grupo()
    {
        $grupo = Grupo::factory()->create();
        $updatedData = [
            'nombre' => 'Grupo Actualizado',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->putJson('/api/v1/grupos/' . $grupo->id, $updatedData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['nombre' => 'Grupo Actualizado']);

        $this->assertDatabaseHas('grupos', ['id' => $grupo->id, 'nombre' => 'Grupo Actualizado']);
    }

    public function test_can_delete_grupo()
    {
        $grupo = Grupo::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')->deleteJson('/api/v1/grupos/' . $grupo->id);

        $response->assertStatus(204);

        $this->assertSoftDeleted('grupos', ['id' => $grupo->id]);
    }
}
