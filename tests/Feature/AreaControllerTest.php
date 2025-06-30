<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Institucion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AreaControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_areas()
    {
        Area::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/areas');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_can_create_area()
    {
        $institucion = Institucion::factory()->create();
        $areaData = [
            'nombre' => 'Nueva Area',
            'institucion_id' => $institucion->id,
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/v1/areas', $areaData);

        $response->assertStatus(201)
                 ->assertJsonFragment(['nombre' => 'Nueva Area']);

        $this->assertDatabaseHas('areas', ['nombre' => 'Nueva Area']);
    }

    public function test_can_show_area()
    {
        $area = Area::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/areas/' . $area->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['nombre' => $area->nombre]);
    }

    public function test_can_update_area()
    {
        $area = Area::factory()->create();
        $updatedData = [
            'nombre' => 'Area Actualizada',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->putJson('/api/v1/areas/' . $area->id, $updatedData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['nombre' => 'Area Actualizada']);

        $this->assertDatabaseHas('areas', ['id' => $area->id, 'nombre' => 'Area Actualizada']);
    }

    public function test_can_delete_area()
    {
        $area = Area::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')->deleteJson('/api/v1/areas/' . $area->id);

        $response->assertStatus(204);

        $this->assertSoftDeleted('areas', ['id' => $area->id]);
    }
}
