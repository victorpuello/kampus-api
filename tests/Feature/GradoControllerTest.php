<?php

namespace Tests\Feature;

use App\Models\Grado;
use App\Models\Institucion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradoControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_grados()
    {
        Grado::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/grados');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_can_create_grado()
    {
        $institucion = Institucion::factory()->create();
        $gradoData = [
            'nombre' => 'Nuevo Grado',
            'nivel' => 1,
            'institucion_id' => $institucion->id,
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/v1/grados', $gradoData);

        $response->assertStatus(201)
                 ->assertJsonFragment(['nombre' => 'Nuevo Grado']);

        $this->assertDatabaseHas('grados', ['nombre' => 'Nuevo Grado']);
    }

    public function test_can_show_grado()
    {
        $grado = Grado::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/grados/' . $grado->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['nombre' => $grado->nombre]);
    }

    public function test_can_update_grado()
    {
        $grado = Grado::factory()->create();
        $updatedData = [
            'nombre' => 'Grado Actualizado',
            'nivel' => 2,
        ];

        $response = $this->actingAs($this->user, 'sanctum')->putJson('/api/v1/grados/' . $grado->id, $updatedData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['nombre' => 'Grado Actualizado']);

        $this->assertDatabaseHas('grados', ['id' => $grado->id, 'nombre' => 'Grado Actualizado', 'nivel' => 2]);
    }

    public function test_can_delete_grado()
    {
        $grado = Grado::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')->deleteJson('/api/v1/grados/' . $grado->id);

        $response->assertStatus(204);

        $this->assertSoftDeleted('grados', ['id' => $grado->id]);
    }
}
