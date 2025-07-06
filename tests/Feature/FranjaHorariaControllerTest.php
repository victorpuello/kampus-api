<?php

namespace Tests\Feature;

use App\Models\FranjaHoraria;
use App\Models\Institucion;
use App\Models\Permission;
use App\Models\Role;
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

        // Crear permisos necesarios
        $permissions = [
            'franjas-horarias.index',
            'franjas-horarias.create',
            'franjas-horarias.show',
            'franjas-horarias.update',
            'franjas-horarias.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['nombre' => $permission]);
        }

        // Crear rol y asignar permisos
        $role = Role::create(['nombre' => 'admin']);
        $role->permissions()->attach(Permission::whereIn('nombre', $permissions)->pluck('id'));

        // Crear usuario y asignar rol
        $this->user = User::factory()->create();
        $this->user->roles()->attach($role->id);
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
            'nombre' => 'Franja Test',
            'hora_inicio' => '08:00',
            'hora_fin' => '09:00',
            'institucion_id' => $institucion->id,
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/v1/franjas-horarias', $franjaData);

        $response->assertStatus(201)
            ->assertJsonFragment(['hora_inicio' => '08:00']);

        // Verificar que se guardó en la base de datos (puede ser datetime completo)
        $this->assertDatabaseHas('franjas_horarias', [
            'nombre' => 'Franja Test',
            'institucion_id' => $institucion->id,
        ]);
    }

    public function test_can_show_franja_horaria()
    {
        $franja = FranjaHoraria::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/franjas-horarias/'.$franja->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'nombre',
                    'hora_inicio',
                    'hora_fin',
                    'institucion_id',
                ],
            ]);

        $this->assertDatabaseHas('franjas_horarias', ['id' => $franja->id]);
    }

    public function test_can_update_franja_horaria()
    {
        $franja = FranjaHoraria::factory()->create();
        $updatedData = [
            'hora_inicio' => '09:00',
            'hora_fin' => '10:00',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->putJson('/api/v1/franjas-horarias/'.$franja->id, $updatedData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'nombre',
                    'hora_inicio',
                    'hora_fin',
                    'institucion_id',
                ],
            ]);

        $this->assertDatabaseHas('franjas_horarias', ['id' => $franja->id]);
    }

    public function test_soft_delete_works_directly()
    {
        $franja = FranjaHoraria::factory()->create();

        // Verificar que el registro existe
        $this->assertDatabaseHas('franjas_horarias', [
            'id' => $franja->id,
            'deleted_at' => null,
        ]);

        // Eliminar directamente
        $franja->delete();

        // Verificar que está marcado como eliminado
        $this->assertSoftDeleted('franjas_horarias', ['id' => $franja->id]);

        // Verificar que no se puede encontrar normalmente
        $this->assertNull(FranjaHoraria::find($franja->id));

        // Verificar que se puede encontrar con withTrashed
        $this->assertNotNull(FranjaHoraria::withTrashed()->find($franja->id));
    }

    public function test_soft_delete_via_api_works()
    {
        $franja = FranjaHoraria::factory()->create();

        // Verificar que el registro existe
        $this->assertDatabaseHas('franjas_horarias', [
            'id' => $franja->id,
            'deleted_at' => null,
        ]);

        // Eliminar vía API
        $response = $this->actingAs($this->user, 'sanctum')->deleteJson('/api/v1/franjas-horarias/'.$franja->id);

        $response->assertStatus(204);

        // Verificar que está marcado como eliminado
        $this->assertSoftDeleted('franjas_horarias', ['id' => $franja->id]);

        // Verificar que no se puede encontrar normalmente
        $this->assertNull(FranjaHoraria::find($franja->id));

        // Verificar que se puede encontrar con withTrashed
        $this->assertNotNull(FranjaHoraria::withTrashed()->find($franja->id));
    }

    public function test_can_delete_franja_horaria()
    {
        $franja = FranjaHoraria::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')->deleteJson('/api/v1/franjas-horarias/'.$franja->id);

        $response->assertStatus(204);

        // Verificar que el registro existe pero está marcado como eliminado
        $this->assertSoftDeleted('franjas_horarias', ['id' => $franja->id]);

        // Verificar que no aparece en las consultas normales
        $this->assertDatabaseMissing('franjas_horarias', [
            'id' => $franja->id,
            'deleted_at' => null,
        ]);

        // Verificar que el modelo no se puede encontrar normalmente
        $this->assertNull(FranjaHoraria::find($franja->id));

        // Verificar que se puede encontrar con withTrashed
        $this->assertNotNull(FranjaHoraria::withTrashed()->find($franja->id));
    }
}
