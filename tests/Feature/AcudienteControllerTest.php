<?php

namespace Tests\Feature;

use App\Models\Acudiente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcudienteControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_acudientes()
    {
        Acudiente::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/acudientes');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_can_create_acudiente()
    {
        $acudienteData = [
            'nombre' => 'Nuevo Acudiente',
            'telefono' => '1234567890',
            'email' => 'nuevo.acudiente@example.com',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/v1/acudientes', $acudienteData);

        $response->assertStatus(201)
                 ->assertJsonFragment(['nombre' => 'Nuevo Acudiente']);

        $this->assertDatabaseHas('acudientes', ['nombre' => 'Nuevo Acudiente']);
    }

    public function test_can_show_acudiente()
    {
        $acudiente = Acudiente::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/acudientes/' . $acudiente->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['nombre' => $acudiente->nombre]);
    }

    public function test_can_update_acudiente()
    {
        $acudiente = Acudiente::factory()->create();
        $updatedData = [
            'nombre' => 'Acudiente Actualizado',
            'telefono' => '0987654321',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->putJson('/api/v1/acudientes/' . $acudiente->id, $updatedData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['nombre' => 'Acudiente Actualizado']);

        $this->assertDatabaseHas('acudientes', ['id' => $acudiente->id, 'nombre' => 'Acudiente Actualizado']);
    }

    public function test_can_delete_acudiente()
    {
        $acudiente = Acudiente::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')->deleteJson('/api/v1/acudientes/' . $acudiente->id);

        $response->assertStatus(204);

        $this->assertSoftDeleted('acudientes', ['id' => $acudiente->id]);
    }
}
