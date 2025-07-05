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

    public function test_can_list_grados()
    {
        // Crear grados para la institución del usuario
        Grado::factory()->count(3)->create([
            'institucion_id' => $this->institucion->id
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/grados');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_can_create_grado()
    {
        $gradoData = [
            'nombre' => 'Nuevo Grado',
            'nivel' => 'Básica Primaria',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/v1/grados', $gradoData);

        $response->assertStatus(201)
                 ->assertJsonFragment(['nombre' => 'Nuevo Grado']);

        $this->assertDatabaseHas('grados', [
            'nombre' => 'Nuevo Grado',
            'institucion_id' => $this->institucion->id
        ]);
    }

    public function test_can_show_grado()
    {
        $grado = Grado::factory()->create([
            'institucion_id' => $this->institucion->id
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/grados/' . $grado->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['nombre' => $grado->nombre]);
    }

    public function test_can_update_grado()
    {
        $grado = Grado::factory()->create([
            'institucion_id' => $this->institucion->id
        ]);
        
        $updatedData = [
            'nombre' => 'Grado Actualizado',
            'nivel' => 'Básica Secundaria',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->putJson('/api/v1/grados/' . $grado->id, $updatedData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['nombre' => 'Grado Actualizado']);

        $this->assertDatabaseHas('grados', ['id' => $grado->id, 'nombre' => 'Grado Actualizado', 'nivel' => 'Básica Secundaria']);
    }

    public function test_can_delete_grado()
    {
        $grado = Grado::factory()->create([
            'institucion_id' => $this->institucion->id
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->deleteJson('/api/v1/grados/' . $grado->id);

        $response->assertStatus(204);

        $this->assertSoftDeleted('grados', ['id' => $grado->id]);
    }
}
