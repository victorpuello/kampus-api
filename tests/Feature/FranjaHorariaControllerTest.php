<?php

namespace Tests\Feature;

use App\Models\FranjaHoraria;
use App\Models\Institucion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FranjaHorariaControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_franjas_horarias()
    {
        FranjaHoraria::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/franjas-horarias');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_can_create_franja_horaria()
    {
        $institucion = Institucion::factory()->create();
        $franjaData = [
            'hora_inicio' => '08:00',
            'hora_fin' => '09:00',
            'institucion_id' => $institucion->id,
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/v1/franjas-horarias', $franjaData);

        $response->assertStatus(201)
                 ->assertJsonFragment(['hora_inicio' => '08:00:00']);

        $this->assertDatabaseHas('franjas_horarias', ['hora_inicio' => '08:00:00']);
    }

    public function test_can_show_franja_horaria()
    {
        $franja = FranjaHoraria::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/franjas-horarias/' . $franja->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['hora_inicio' => $franja->hora_inicio->format('H:i:s')]);
    }

    public function test_can_update_franja_horaria()
    {
        $franja = FranjaHoraria::factory()->create();
        $updatedData = [
            'hora_inicio' => '09:00',
            'hora_fin' => '10:00',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->putJson('/api/v1/franjas-horarias/' . $franja->id, $updatedData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['hora_inicio' => '09:00:00']);

        $this->assertDatabaseHas('franjas_horarias', ['id' => $franja->id, 'hora_inicio' => '09:00:00', 'hora_fin' => '10:00:00']);
    }

    public function test_can_delete_franja_horaria()
    {
        $franja = FranjaHoraria::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')->deleteJson('/api/v1/franjas-horarias/' . $franja->id);

        $response->assertStatus(204);

        $this->assertSoftDeleted('franjas_horarias', ['id' => $franja->id]);
    }
}
