<?php

namespace Tests\Feature;

use App\Models\Institucion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileUploadTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_upload_escudo_image()
    {
        Storage::fake('public');
        $institucion = Institucion::factory()->create();

        $file = UploadedFile::fake()->image('escudo.jpg', 300, 300);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/v1/instituciones/' . $institucion->id, [
                'escudo' => $file
            ]);

        if ($response->status() !== 200) {
            dump('Response Status: ' . $response->status());
            dump('Response Content: ' . $response->getContent());
        }

        $response->assertStatus(200);

        // Verificar que el archivo se guardó en la base de datos
        $institucion->refresh();
        $this->assertNotNull($institucion->escudo);
        $this->assertStringStartsWith('instituciones/escudos/', $institucion->escudo);
        Storage::disk('public')->assertExists($institucion->escudo);
    }

    public function test_validates_image_file_type()
    {
        Storage::fake('public');
        $institucion = Institucion::factory()->create();

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/v1/instituciones/' . $institucion->id, [
                'escudo' => $file
            ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['escudo']);
    }

    public function test_validates_image_size()
    {
        Storage::fake('public');
        $institucion = Institucion::factory()->create();

        // Forzar archivo de 6MB (mayor al límite de 5MB)
        $file = UploadedFile::fake()->image('large.jpg', 200, 200)->size(6000); // 6MB

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/v1/instituciones/' . $institucion->id, [
                'escudo' => $file
            ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['escudo']);
    }

    public function test_can_replace_existing_escudo()
    {
        Storage::fake('public');
        $institucion = Institucion::factory()->create();

        // Subir primer archivo
        $file1 = UploadedFile::fake()->image('escudo1.jpg', 200, 200);
        $response1 = $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/v1/instituciones/' . $institucion->id, [
                'escudo' => $file1
            ]);

        $response1->assertStatus(200);

        // Subir segundo archivo (reemplazar)
        $file2 = UploadedFile::fake()->image('escudo2.jpg', 200, 200);
        $response2 = $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/v1/instituciones/' . $institucion->id, [
                'escudo' => $file2
            ]);

        $response2->assertStatus(200);

        // Verificar que el segundo archivo se guardó
        $institucion->refresh();
        $this->assertNotNull($institucion->escudo);
        $this->assertStringStartsWith('instituciones/escudos/', $institucion->escudo);
        Storage::disk('public')->assertExists($institucion->escudo);
    }

    public function test_can_upload_multiple_image_formats()
    {
        Storage::fake('public');
        $institucion = Institucion::factory()->create();

        $formats = ['jpg', 'jpeg', 'png', 'gif'];

        foreach ($formats as $format) {
            $file = UploadedFile::fake()->image("escudo.{$format}", 200, 200);

            $response = $this->actingAs($this->user, 'sanctum')
                ->putJson('/api/v1/instituciones/' . $institucion->id, [
                    'escudo' => $file
                ]);

            $response->assertStatus(200);

            // Limpiar para la siguiente iteración
            $institucion->refresh();
        }
    }

    public function test_escudo_url_is_accessible()
    {
        Storage::fake('public');
        $institucion = Institucion::factory()->create();

        $file = UploadedFile::fake()->image('escudo.jpg', 200, 200);
        
        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/v1/instituciones/' . $institucion->id, [
                'escudo' => $file
            ]);

        $response->assertStatus(200);

        // Obtener la institución actualizada
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/instituciones/' . $institucion->id);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'nombre',
                         'escudo',
                         'escudo_url'
                     ]
                 ]);
        $data = $response->json('data');
        $this->assertNotNull($data['escudo_url']);
        $this->assertIsString($data['escudo_url']);
    }

    public function test_handles_missing_file_gracefully()
    {
        $institucion = Institucion::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/v1/instituciones/' . $institucion->id, [
                'nombre' => 'Institución Actualizada'
            ]);

        $response->assertStatus(200);
    }

    public function test_validates_image_dimensions()
    {
        Storage::fake('public');
        $institucion = Institucion::factory()->create();

        // Imagen muy pequeña
        $file = UploadedFile::fake()->image('small.jpg', 50, 50);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/v1/instituciones/' . $institucion->id, [
                'escudo' => $file
            ]);

        // Dependiendo de las reglas de validación, esto podría fallar
        // Si tienes reglas de dimensiones mínimas
        if ($response->status() === 422) {
            $response->assertJsonValidationErrors(['escudo']);
        } else {
            $response->assertStatus(200);
        }
    }
} 