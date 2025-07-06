<?php

namespace Tests\Feature;

use App\Models\Anio;
use App\Models\Institucion;
use App\Models\Periodo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PeriodoControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Anio $anio;

    private Periodo $periodo;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear usuario autenticado
        $this->user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('123456'),
        ]);

        // Crear institución
        $institucion = Institucion::factory()->create([
            'nombre' => 'Instituto de Prueba',
            'siglas' => 'ITP',
        ]);

        // Crear año académico
        $this->anio = Anio::factory()->create([
            'nombre' => '2024-2025',
            'fecha_inicio' => '2024-01-15',
            'fecha_fin' => '2024-12-15',
            'institucion_id' => $institucion->id,
            'estado' => 'activo',
        ]);

        // Crear periodo
        $this->periodo = Periodo::factory()->create([
            'nombre' => 'Primer Periodo',
            'fecha_inicio' => '2024-01-15',
            'fecha_fin' => '2024-04-15',
            'anio_id' => $this->anio->id,
        ]);

        // Autenticar usuario
        Sanctum::actingAs($this->user);
    }

    /** @test */
    public function puede_listar_periodos()
    {
        $response = $this->getJson('/api/v1/periodos');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'nombre',
                        'fecha_inicio',
                        'fecha_fin',
                        'anio_id',
                    ],
                ],
            ]);
    }

    /** @test */
    public function puede_listar_periodos_por_año_academico()
    {
        $response = $this->getJson("/api/v1/anios/{$this->anio->id}/periodos");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'nombre',
                        'fecha_inicio',
                        'fecha_fin',
                        'anio_id',
                    ],
                ],
            ]);
    }

    /** @test */
    public function puede_crear_periodo_valido()
    {
        $data = [
            'nombre' => 'Segundo Periodo',
            'fecha_inicio' => '2024-05-01',
            'fecha_fin' => '2024-08-15',
            'anio_id' => $this->anio->id,
        ];

        $response = $this->postJson('/api/v1/periodos', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'nombre',
                    'fecha_inicio',
                    'fecha_fin',
                    'anio_id',
                ],
            ]);

        $this->assertDatabaseHas('periodos', [
            'nombre' => 'Segundo Periodo',
            'anio_id' => $this->anio->id,
        ]);
    }

    /** @test */
    public function puede_crear_periodo_desde_año_academico()
    {
        $data = [
            'nombre' => 'Tercer Periodo',
            'fecha_inicio' => '2024-09-01',
            'fecha_fin' => '2024-12-15',
            'anio_id' => $this->anio->id,
        ];

        $response = $this->postJson("/api/v1/anios/{$this->anio->id}/periodos", $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'nombre',
                    'fecha_inicio',
                    'fecha_fin',
                    'anio_id',
                ],
            ]);
    }

    /** @test */
    public function no_puede_crear_periodo_con_fecha_inicio_antes_del_año()
    {
        $data = [
            'nombre' => 'Periodo Inválido',
            'fecha_inicio' => '2023-12-01', // Antes del año académico
            'fecha_fin' => '2024-03-15',
            'anio_id' => $this->anio->id,
        ];

        $response = $this->postJson('/api/v1/periodos', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['fecha_inicio']);
    }

    /** @test */
    public function no_puede_crear_periodo_con_fecha_fin_despues_del_año()
    {
        $data = [
            'nombre' => 'Periodo Inválido',
            'fecha_inicio' => '2024-10-01',
            'fecha_fin' => '2025-01-15', // Después del año académico
            'anio_id' => $this->anio->id,
        ];

        $response = $this->postJson('/api/v1/periodos', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['fecha_fin']);
    }

    /** @test */
    public function no_puede_crear_periodo_con_fechas_solapadas()
    {
        $data = [
            'nombre' => 'Periodo Solapado',
            'fecha_inicio' => '2024-02-01', // Se solapa con el periodo existente
            'fecha_fin' => '2024-05-15',
            'anio_id' => $this->anio->id,
        ];

        $response = $this->postJson('/api/v1/periodos', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['fecha_inicio']);
    }

    /** @test */
    public function puede_mostrar_periodo()
    {
        $response = $this->getJson("/api/v1/periodos/{$this->periodo->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $this->periodo->id,
                    'nombre' => $this->periodo->nombre,
                    'anio_id' => $this->periodo->anio_id,
                ],
            ]);
    }

    /** @test */
    public function puede_actualizar_periodo()
    {
        $data = [
            'nombre' => 'Periodo Actualizado',
            'fecha_inicio' => '2024-05-01',
            'fecha_fin' => '2024-08-15',
            'anio_id' => $this->anio->id,
        ];

        $response = $this->putJson("/api/v1/periodos/{$this->periodo->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $this->periodo->id,
                    'nombre' => 'Periodo Actualizado',
                ],
            ]);

        $this->assertDatabaseHas('periodos', [
            'id' => $this->periodo->id,
            'nombre' => 'Periodo Actualizado',
        ]);
    }

    /** @test */
    public function no_puede_actualizar_periodo_con_fechas_solapadas()
    {
        // Crear un segundo periodo
        $segundoPeriodo = Periodo::factory()->create([
            'nombre' => 'Segundo Periodo',
            'fecha_inicio' => '2024-05-01',
            'fecha_fin' => '2024-08-15',
            'anio_id' => $this->anio->id,
        ]);

        $data = [
            'nombre' => 'Periodo Actualizado',
            'fecha_inicio' => '2024-06-01', // Se solapa con el segundo periodo
            'fecha_fin' => '2024-09-15',
            'anio_id' => $this->anio->id,
        ];

        $response = $this->putJson("/api/v1/periodos/{$this->periodo->id}", $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['fecha_inicio']);
    }

    /** @test */
    public function puede_eliminar_periodo()
    {
        $this->actingAs($this->user);
        $response = $this->deleteJson("/api/v1/periodos/{$this->periodo->id}");
        $response->assertStatus(200);
        // El modelo Periodo usa SoftDeletes, por lo que debemos verificar que esté soft deleted
        $this->assertSoftDeleted('periodos', [
            'id' => $this->periodo->id,
        ]);
    }

    /** @test */
    public function puede_buscar_periodos()
    {
        $response = $this->getJson('/api/v1/periodos?search=Primer');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'nombre',
                        'fecha_inicio',
                        'fecha_fin',
                        'anio_id',
                    ],
                ],
            ]);
    }

    /** @test */
    public function puede_paginar_periodos()
    {
        $this->actingAs($this->user);

        // Crear periodos adicionales para probar paginación
        Periodo::factory()->count(15)->create([
            'anio_id' => $this->anio->id,
        ]);

        $response = $this->getJson('/api/v1/periodos?page=1&per_page=10');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'current_page',
            'last_page',
            'per_page',
            'total',
        ]);

        $data = $response->json();
        $this->assertEquals(1, $data['current_page']);
        $this->assertEquals(10, $data['per_page']);
        $this->assertCount(10, $data['data']);
    }

    /** @test */
    public function retorna_404_si_periodo_no_existe()
    {
        $response = $this->getJson('/api/v1/periodos/99999');

        $response->assertStatus(404);
    }

    /** @test */
    public function requiere_autenticacion()
    {
        // Desautenticar el usuario actual
        $this->app['auth']->forgetGuards();

        // No usar actingAs para simular usuario no autenticado
        $response = $this->getJson('/api/v1/periodos');
        $response->assertStatus(401);
    }

    /** @test */
    public function validacion_campos_requeridos()
    {
        $response = $this->postJson('/api/v1/periodos', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nombre', 'fecha_inicio', 'fecha_fin', 'anio_id']);
    }

    /** @test */
    public function validacion_formato_fechas()
    {
        $data = [
            'nombre' => 'Periodo Test',
            'fecha_inicio' => 'fecha-invalida',
            'fecha_fin' => 'otra-fecha-invalida',
            'anio_id' => $this->anio->id,
        ];

        $response = $this->postJson('/api/v1/periodos', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['fecha_inicio', 'fecha_fin']);
    }

    /** @test */
    public function validacion_fecha_inicio_debe_ser_menor_que_fecha_fin()
    {
        $data = [
            'nombre' => 'Periodo Test',
            'fecha_inicio' => '2024-08-15',
            'fecha_fin' => '2024-05-01', // Fecha fin antes que inicio
            'anio_id' => $this->anio->id,
        ];

        $response = $this->postJson('/api/v1/periodos', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['fecha_fin']);
    }
}
