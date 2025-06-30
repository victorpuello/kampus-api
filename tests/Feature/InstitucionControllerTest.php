<?php

namespace Tests\Feature;

use App\Models\Institucion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstitucionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(); // Crea un usuario para autenticación
    }

    public function test_can_list_instituciones()
    {
        Institucion::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/instituciones');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_can_create_institucion()
    {
        $institucionData = [
            'nombre' => 'Nueva Institucion',
            'siglas' => 'NI',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/v1/instituciones', $institucionData);

        $response->assertStatus(201)
                 ->assertJsonFragment(['nombre' => 'Nueva Institucion']);

        $this->assertDatabaseHas('instituciones', ['nombre' => 'Nueva Institucion']);
    }

    public function test_can_show_institucion()
    {
        $institucion = Institucion::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/instituciones/' . $institucion->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['nombre' => $institucion->nombre]);
    }

    public function test_can_update_institucion()
    {
        $institucion = Institucion::factory()->create();
        $updatedData = [
            'nombre' => 'Institucion Actualizada',
            'siglas' => 'IA',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->putJson('/api/v1/instituciones/' . $institucion->id, $updatedData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['nombre' => 'Institucion Actualizada']);

        $this->assertDatabaseHas('instituciones', ['id' => $institucion->id, 'nombre' => 'Institucion Actualizada']);
    }

    public function test_can_delete_institucion()
    {
        $institucion = Institucion::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')->deleteJson('/api/v1/instituciones/' . $institucion->id);

        $response->assertStatus(204);

        $this->assertSoftDeleted('instituciones', ['id' => $institucion->id]);
    }
}
