<?php

namespace Tests\Feature;

use App\Models\Anio;
use App\Models\Institucion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnioControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_anios()
    {
        Anio::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/anios');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_can_create_anio()
    {
        $institucion = Institucion::factory()->create();
        $anioData = [
            'nombre' => '2025-2026',
            'fecha_inicio' => '2025-09-01',
            'fecha_fin' => '2026-06-30',
            'institucion_id' => $institucion->id,
            'estado' => 'activo',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/v1/anios', $anioData);

        $response->assertStatus(201)
                 ->assertJsonFragment(['nombre' => '2025-2026']);

        $this->assertDatabaseHas('anios', ['nombre' => '2025-2026']);
    }

    public function test_can_show_anio()
    {
        $anio = Anio::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/anios/' . $anio->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['nombre' => $anio->nombre]);
    }

    public function test_can_update_anio()
    {
        $anio = Anio::factory()->create();
        $updatedData = [
            'nombre' => '2025-2026 Actualizado',
            'estado' => 'inactivo',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->putJson('/api/v1/anios/' . $anio->id, $updatedData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['nombre' => '2025-2026 Actualizado']);

        $this->assertDatabaseHas('anios', ['id' => $anio->id, 'nombre' => '2025-2026 Actualizado', 'estado' => 'inactivo']);
    }

    public function test_can_delete_anio()
    {
        $anio = Anio::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')->deleteJson('/api/v1/anios/' . $anio->id);

        $response->assertStatus(204);

        $this->assertSoftDeleted('anios', ['id' => $anio->id]);
    }
}
