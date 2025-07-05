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
    protected $institucion;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear institución
        $this->institucion = Institucion::factory()->create();
        
        // Crear usuario con la institución
        $this->user = User::factory()->create([
            'institucion_id' => $this->institucion->id
        ]);
    }

    public function test_can_list_areas()
    {
        // Crear áreas para la institución del usuario
        Area::factory()->count(3)->create([
            'institucion_id' => $this->institucion->id
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/areas');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_can_create_area()
    {
        $areaData = [
            'nombre' => 'Nueva Area',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/v1/areas', $areaData);

        $response->assertStatus(201)
                 ->assertJsonFragment(['nombre' => 'Nueva Area']);

        $this->assertDatabaseHas('areas', [
            'nombre' => 'Nueva Area',
            'institucion_id' => $this->institucion->id
        ]);
    }

    public function test_can_show_area()
    {
        $area = Area::factory()->create([
            'institucion_id' => $this->institucion->id
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/areas/' . $area->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['nombre' => $area->nombre]);
    }

    public function test_can_update_area()
    {
        $area = Area::factory()->create([
            'institucion_id' => $this->institucion->id
        ]);
        
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
        $area = Area::factory()->create([
            'institucion_id' => $this->institucion->id
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->deleteJson('/api/v1/areas/' . $area->id);

        $response->assertStatus(204);

        $this->assertSoftDeleted('areas', ['id' => $area->id]);
    }
}
