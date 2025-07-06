<?php

namespace Tests\Feature;

use App\Models\Institucion;
use App\Models\Sede;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $institucion;

    protected $sede;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->institucion = Institucion::factory()->create();
        $this->sede = Sede::factory()->create(['institucion_id' => $this->institucion->id]);
    }

    public function test_instituciones_routes_are_accessible()
    {
        $this->actingAs($this->user, 'sanctum');

        // Listar instituciones
        $response = $this->getJson('/api/v1/instituciones');
        $response->assertStatus(200);

        // Crear institución
        $response = $this->postJson('/api/v1/instituciones', [
            'nombre' => 'Test Institution',
            'siglas' => 'TI',
        ]);
        $response->assertStatus(201);

        // Ver institución específica
        $response = $this->getJson('/api/v1/instituciones/'.$this->institucion->id);
        $response->assertStatus(200);

        // Actualizar institución
        $response = $this->putJson('/api/v1/instituciones/'.$this->institucion->id, [
            'nombre' => 'Updated Institution',
        ]);
        $response->assertStatus(200);

        // Eliminar institución
        $response = $this->deleteJson('/api/v1/instituciones/'.$this->institucion->id);
        $response->assertStatus(204);
    }

    public function test_sedes_routes_are_accessible()
    {
        $this->actingAs($this->user, 'sanctum');

        // Listar sedes
        $response = $this->getJson('/api/v1/sedes');
        $response->assertStatus(200);

        // Crear sede
        $response = $this->postJson('/api/v1/sedes', [
            'institucion_id' => $this->institucion->id,
            'nombre' => 'Test Sede',
            'direccion' => 'Test Address',
        ]);
        $response->assertStatus(201);

        // Ver sede específica
        $response = $this->getJson('/api/v1/sedes/'.$this->sede->id);
        $response->assertStatus(200);

        // Actualizar sede
        $response = $this->putJson('/api/v1/sedes/'.$this->sede->id, [
            'nombre' => 'Updated Sede',
        ]);
        $response->assertStatus(200);

        // Eliminar sede
        $response = $this->deleteJson('/api/v1/sedes/'.$this->sede->id);
        $response->assertStatus(204);
    }

    public function test_institucion_sedes_relationship_route()
    {
        $this->actingAs($this->user, 'sanctum');

        // Crear más sedes para la institución
        Sede::factory()->count(2)->create(['institucion_id' => $this->institucion->id]);

        // Obtener sedes de una institución específica
        $response = $this->getJson('/api/v1/instituciones/'.$this->institucion->id.'/sedes');
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data'); // La sede original + 2 nuevas
    }

    public function test_routes_return_correct_json_structure()
    {
        $this->actingAs($this->user, 'sanctum');

        // Verificar estructura de respuesta para instituciones
        $response = $this->getJson('/api/v1/instituciones');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'nombre',
                        'siglas',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);

        // Verificar estructura de respuesta para sedes
        $response = $this->getJson('/api/v1/sedes');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'nombre',
                        'direccion',
                        'institucion_id',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_routes_require_authentication()
    {
        // Sin autenticación, todas las rutas deben devolver 401
        $routes = [
            'GET /api/v1/instituciones',
            'POST /api/v1/instituciones',
            'GET /api/v1/instituciones/1',
            'PUT /api/v1/instituciones/1',
            'DELETE /api/v1/instituciones/1',
            'GET /api/v1/sedes',
            'POST /api/v1/sedes',
            'GET /api/v1/sedes/1',
            'PUT /api/v1/sedes/1',
            'DELETE /api/v1/sedes/1',
        ];

        foreach ($routes as $route) {
            [$method, $path] = explode(' ', $route);

            if ($method === 'GET') {
                $response = $this->getJson($path);
            } elseif ($method === 'POST') {
                $response = $this->postJson($path, []);
            } elseif ($method === 'PUT') {
                $response = $this->putJson($path, []);
            } elseif ($method === 'DELETE') {
                $response = $this->deleteJson($path);
            }

            $response->assertStatus(401);
        }
    }

    public function test_routes_handle_invalid_ids()
    {
        $this->actingAs($this->user, 'sanctum');

        // Intentar acceder a instituciones inexistentes
        $response = $this->getJson('/api/v1/instituciones/999');
        $response->assertStatus(404);

        $response = $this->putJson('/api/v1/instituciones/999', ['nombre' => 'Test']);
        $response->assertStatus(404);

        $response = $this->deleteJson('/api/v1/instituciones/999');
        $response->assertStatus(404);

        // Intentar acceder a sedes inexistentes
        $response = $this->getJson('/api/v1/sedes/999');
        $response->assertStatus(404);

        $response = $this->putJson('/api/v1/sedes/999', ['nombre' => 'Test']);
        $response->assertStatus(404);

        $response = $this->deleteJson('/api/v1/sedes/999');
        $response->assertStatus(404);
    }

    public function test_routes_support_pagination()
    {
        $this->actingAs($this->user, 'sanctum');

        // Crear múltiples instituciones
        Institucion::factory()->count(15)->create();

        // Verificar paginación en instituciones
        $response = $this->getJson('/api/v1/instituciones?page=1&per_page=10');
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

        // Crear múltiples sedes
        Sede::factory()->count(15)->create(['institucion_id' => $this->institucion->id]);

        // Verificar paginación en sedes
        $response = $this->getJson('/api/v1/sedes?page=1&per_page=10');
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

    public function test_routes_support_search()
    {
        $this->actingAs($this->user, 'sanctum');

        // Crear instituciones con nombres específicos
        Institucion::factory()->create(['nombre' => 'Colegio San José']);
        Institucion::factory()->create(['nombre' => 'Instituto Técnico']);

        // Buscar instituciones
        $response = $this->getJson('/api/v1/instituciones?search=San José');
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');

        // Crear sedes con nombres específicos
        Sede::factory()->create([
            'institucion_id' => $this->institucion->id,
            'nombre' => 'Sede Norte',
        ]);
        Sede::factory()->create([
            'institucion_id' => $this->institucion->id,
            'nombre' => 'Sede Sur',
        ]);

        // Buscar sedes
        $response = $this->getJson('/api/v1/sedes?search=Norte');
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }
}
