<?php

namespace Tests\Feature;

use App\Models\Institucion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
        Institucion::query()->delete(); // Limpia la tabla antes de crear
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
            'slogan' => 'Educación de calidad',
            'dane' => '123456789',
            'resolucion_aprobacion' => 'RES-001-2024',
            'direccion' => 'Calle 123 #45-67',
            'telefono' => '6012345678',
            'email' => 'info@nuevainstitucion.edu.co',
            'rector' => 'Dr. Juan Pérez',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/v1/instituciones', $institucionData);

        $response->assertStatus(201)
            ->assertJsonFragment(['nombre' => 'Nueva Institucion']);

        $this->assertDatabaseHas('instituciones', ['nombre' => 'Nueva Institucion']);
    }

    public function test_can_show_institucion()
    {
        $institucion = Institucion::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/instituciones/'.$institucion->id);

        $response->assertStatus(200)
            ->assertJsonFragment(['nombre' => $institucion->nombre]);
    }

    public function test_can_update_institucion()
    {
        $institucion = Institucion::factory()->create();
        $updatedData = [
            'nombre' => 'Institucion Actualizada',
            'siglas' => 'IA',
            'slogan' => 'Nuevo slogan',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->putJson('/api/v1/instituciones/'.$institucion->id, $updatedData);

        $response->assertStatus(200)
            ->assertJsonFragment(['nombre' => 'Institucion Actualizada']);

        $this->assertDatabaseHas('instituciones', ['id' => $institucion->id, 'nombre' => 'Institucion Actualizada']);
    }

    public function test_can_delete_institucion()
    {
        $institucion = Institucion::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')->deleteJson('/api/v1/instituciones/'.$institucion->id);

        $response->assertStatus(204);

        $this->assertSoftDeleted('instituciones', ['id' => $institucion->id]);
    }

    public function test_can_upload_escudo()
    {
        $institucion = Institucion::factory()->create();

        $file = UploadedFile::fake()->image('escudo.jpg', 300, 300); // Dimensiones válidas

        $response = $this->actingAs($this->user, 'sanctum')->putJson("/api/v1/instituciones/{$institucion->id}", [
            'nombre' => $institucion->nombre,
            'siglas' => $institucion->siglas,
            'escudo' => $file,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id', 'nombre', 'siglas', 'escudo',
                ],
            ]);

        $institucion->refresh();
        $this->assertNotNull($institucion->escudo);
        $this->assertStringStartsWith('instituciones/escudos/', $institucion->escudo);
        Storage::disk('public')->assertExists($institucion->escudo);
    }

    public function test_uses_default_escudo_when_no_escudo_provided()
    {
        $institucion = Institucion::factory()->create([
            'escudo' => null,
        ]);

        // Verificar que se asigna la imagen por defecto
        $this->assertEquals('instituciones/escudos/default.png', $institucion->escudo);

        // Verificar que la URL apunta a la imagen por defecto
        $this->assertEquals(
            asset('storage/instituciones/escudos/default.png'),
            $institucion->getFileUrl('escudo')
        );
    }

    public function test_uses_default_escudo_when_escudo_file_missing()
    {
        $institucion = Institucion::factory()->create([
            'escudo' => 'instituciones/escudos/nonexistent.png',
        ]);

        // Verificar que se asigna la imagen por defecto cuando el archivo no existe
        $this->assertEquals('instituciones/escudos/default.png', $institucion->escudo);

        // Verificar que la URL apunta a la imagen por defecto
        $this->assertEquals(
            asset('storage/instituciones/escudos/default.png'),
            $institucion->getFileUrl('escudo')
        );
    }

    public function test_validates_required_fields_on_create()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/instituciones', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nombre']);
    }

    public function test_validates_email_format()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/instituciones', [
                'nombre' => 'Test Institution',
                'email' => 'invalid-email',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_returns_404_for_nonexistent_institucion()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/instituciones/999');

        $response->assertStatus(404);
    }

    public function test_can_get_institucion_with_sedes()
    {
        $institucion = Institucion::factory()->create();
        $institucion->sedes()->create([
            'nombre' => 'Sede Principal',
            'direccion' => 'Calle 123',
            'telefono' => '1234567',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/instituciones/'.$institucion->id.'?include=sedes');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'nombre',
                    'sedes' => [
                        '*' => ['id', 'nombre', 'direccion'],
                    ],
                ],
            ]);
    }

    public function test_unauthorized_user_cannot_access_instituciones()
    {
        $response = $this->getJson('/api/v1/instituciones');

        $response->assertStatus(401);
    }

    public function test_can_paginate_instituciones()
    {
        Institucion::factory()->count(15)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/instituciones?page=1&per_page=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta' => [
                    'current_page',
                    'per_page',
                    'total',
                ],
            ]);
    }
}
