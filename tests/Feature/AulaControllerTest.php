<?php

namespace Tests\Feature;

use App\Models\Aula;
use App\Models\Institucion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AulaControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_aulas()
    {
        Aula::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/aulas');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_can_create_aula()
    {
        $institucion = Institucion::factory()->create();
        $aulaData = [
            'nombre' => 'Nueva Aula',
            'capacidad' => 30,
            'institucion_id' => $institucion->id,
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/v1/aulas', $aulaData);

        $response->assertStatus(201)
                 ->assertJsonFragment(['nombre' => 'Nueva Aula']);

        $this->assertDatabaseHas('aulas', ['nombre' => 'Nueva Aula']);
    }

    public function test_can_show_aula()
    {
        $aula = Aula::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/aulas/' . $aula->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['nombre' => $aula->nombre]);
    }

    public function test_can_update_aula()
    {
        $aula = Aula::factory()->create();
        $updatedData = [
            'nombre' => 'Aula Actualizada',
            'capacidad' => 35,
        ];

        $response = $this->actingAs($this->user, 'sanctum')->putJson('/api/v1/aulas/' . $aula->id, $updatedData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['nombre' => 'Aula Actualizada']);

        $this->assertDatabaseHas('aulas', ['id' => $aula->id, 'nombre' => 'Aula Actualizada', 'capacidad' => 35]);
    }

    public function test_can_delete_aula()
    {
        $aula = Aula::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')->deleteJson('/api/v1/aulas/' . $aula->id);

        $response->assertStatus(204);

        $this->assertSoftDeleted('aulas', ['id' => $aula->id]);
    }
}
