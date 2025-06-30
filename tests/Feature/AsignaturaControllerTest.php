<?php

namespace Tests\Feature;

use App\Models\Asignatura;
use App\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsignaturaControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_asignaturas()
    {
        Asignatura::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/asignaturas');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_can_create_asignatura()
    {
        $area = Area::factory()->create();
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
        $asignatura = Asignatura::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/asignaturas/' . $asignatura->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['nombre' => $asignatura->nombre]);
    }

    public function test_can_update_asignatura()
    {
        $asignatura = Asignatura::factory()->create();
        $updatedData = [
            'nombre' => 'Asignatura Actualizada',
            'porcentaje_area' => 75.00,
        ];

        $response = $this->actingAs($this->user, 'sanctum')->putJson('/api/v1/asignaturas/' . $asignatura->id, $updatedData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['nombre' => 'Asignatura Actualizada']);

        $this->assertDatabaseHas('asignaturas', ['id' => $asignatura->id, 'nombre' => 'Asignatura Actualizada', 'porcentaje_area' => 75.00]);
    }

    public function test_can_delete_asignatura()
    {
        $asignatura = Asignatura::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')->deleteJson('/api/v1/asignaturas/' . $asignatura->id);

        $response->assertStatus(204);

        $this->assertSoftDeleted('asignaturas', ['id' => $asignatura->id]);
    }
}
