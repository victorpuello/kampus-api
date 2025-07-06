<?php

namespace Tests\Feature;

use App\Models\Anio;
use App\Models\Estudiante;
use App\Models\Grado;
use App\Models\Grupo;
use App\Models\Institucion;
use App\Models\Sede;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StudentGradoGrupoSedeTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $user;

    protected $institucion;

    protected $sede;

    protected $grado;

    protected $grupo;

    protected $anio;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear datos base para las pruebas
        $this->institucion = Institucion::factory()->create();
        $this->sede = Sede::factory()->create(['institucion_id' => $this->institucion->id]);
        $this->anio = Anio::factory()->create();
        $this->grado = Grado::factory()->create(['institucion_id' => $this->institucion->id]);
        $this->grupo = Grupo::factory()->create([
            'sede_id' => $this->sede->id,
            'grado_id' => $this->grado->id,
            'anio_id' => $this->anio->id,
        ]);

        // Crear usuario admin para las pruebas
        $this->user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('123456'),
            'institucion_id' => $this->institucion->id,
        ]);
    }

    /** @test */
    public function puede_crear_estudiante_con_relaciones_completas()
    {
        $userData = [
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'email' => 'juan.perez@test.com',
            'tipo_documento' => 'CC',
            'numero_documento' => '12345678',
            'username' => 'juan.perez',
            'password' => 'password123',
            'institucion_id' => $this->institucion->id,
            'estado' => 'activo',
        ];

        $studentData = [
            'codigo_estudiantil' => 'EST001',
            'fecha_nacimiento' => '2005-01-15',
            'genero' => 'M',
            'direccion' => 'Calle 123 #45-67',
            'telefono' => '3001234567',
            'estado' => 'activo',
            'grupo_id' => $this->grupo->id,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/estudiantes', array_merge($userData, $studentData));

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'email' => 'juan.perez@test.com',
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
        ]);

        $this->assertDatabaseHas('estudiantes', [
            'codigo_estudiantil' => 'EST001',
            'grupo_id' => $this->grupo->id,
        ]);

        // Verificar que el estudiante tiene las relaciones correctas
        $estudiante = Estudiante::where('codigo_estudiantil', 'EST001')->first();
        $this->assertNotNull($estudiante->user);
        $this->assertNotNull($estudiante->grupo);
        $this->assertEquals($this->grupo->id, $estudiante->grupo_id);
    }

    /** @test */
    public function puede_obtener_estudiante_con_relaciones_cargadas()
    {
        $estudiante = Estudiante::factory()->create([
            'grupo_id' => $this->grupo->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/estudiantes/{$estudiante->id}");

        $response->assertStatus(200);

        $data = $response->json('data');

        // Verificar que las relaciones están presentes
        $this->assertArrayHasKey('user', $data);
        $this->assertArrayHasKey('grupo', $data);
        $this->assertArrayHasKey('institucion', $data);

        // Verificar datos del usuario
        $this->assertArrayHasKey('nombre', $data['user']);
        $this->assertArrayHasKey('apellido', $data['user']);
        $this->assertArrayHasKey('email', $data['user']);

        // Verificar datos del grupo
        $this->assertArrayHasKey('nombre', $data['grupo']);
        $this->assertArrayHasKey('sede', $data['grupo']);
        $this->assertArrayHasKey('grado', $data['grupo']);

        // Verificar datos de la sede
        $this->assertArrayHasKey('nombre', $data['grupo']['sede']);
        $this->assertArrayHasKey('institucion', $data['grupo']['sede']);

        // Verificar datos del grado
        $this->assertArrayHasKey('nombre', $data['grupo']['grado']);
        $this->assertArrayHasKey('nivel', $data['grupo']['grado']);
    }

    /** @test */
    public function puede_listar_estudiantes_con_relaciones()
    {
        // Crear varios estudiantes
        Estudiante::factory()->count(3)->create([
            'grupo_id' => $this->grupo->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/estudiantes');

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(3, $data);

        // Verificar que cada estudiante tiene las relaciones necesarias
        foreach ($data as $estudiante) {
            $this->assertArrayHasKey('user', $estudiante);
            $this->assertArrayHasKey('grupo', $estudiante);
            $this->assertNotNull($estudiante['user']);
            $this->assertNotNull($estudiante['grupo']);
        }
    }

    /** @test */
    public function puede_actualizar_estudiante_cambiando_grupo()
    {
        $estudiante = Estudiante::factory()->create([
            'grupo_id' => $this->grupo->id,
        ]);

        // Crear un nuevo grupo
        $nuevoGrupo = Grupo::factory()->create([
            'sede_id' => $this->sede->id,
            'grado_id' => $this->grado->id,
            'anio_id' => $this->anio->id,
        ]);

        $updateData = [
            'grupo_id' => $nuevoGrupo->id,
            'estado' => 'activo',
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/v1/estudiantes/{$estudiante->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('estudiantes', [
            'id' => $estudiante->id,
            'grupo_id' => $nuevoGrupo->id,
        ]);
    }

    /** @test */
    public function puede_eliminar_estudiante()
    {
        $estudiante = Estudiante::factory()->create([
            'grupo_id' => $this->grupo->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/estudiantes/{$estudiante->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('estudiantes', [
            'id' => $estudiante->id,
        ]);
    }

    /** @test */
    public function puede_buscar_estudiantes_por_nombre()
    {
        $estudiante1 = Estudiante::factory()->create([
            'grupo_id' => $this->grupo->id,
        ]);
        $estudiante1->user->update(['nombre' => 'María', 'apellido' => 'García']);

        $estudiante2 = Estudiante::factory()->create([
            'grupo_id' => $this->grupo->id,
        ]);
        $estudiante2->user->update(['nombre' => 'Carlos', 'apellido' => 'López']);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/estudiantes?search=María');

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('María', $data[0]['user']['nombre']);
    }

    /** @test */
    public function puede_buscar_estudiantes_por_grupo()
    {
        $estudiante1 = Estudiante::factory()->create([
            'grupo_id' => $this->grupo->id,
        ]);

        $otroGrupo = Grupo::factory()->create([
            'sede_id' => $this->sede->id,
            'grado_id' => $this->grado->id,
            'anio_id' => $this->anio->id,
        ]);

        $estudiante2 = Estudiante::factory()->create([
            'grupo_id' => $otroGrupo->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/estudiantes?grupo_id={$this->grupo->id}");

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals($this->grupo->id, $data[0]['grupo']['id']);
    }

    /** @test */
    public function puede_obtener_estudiantes_por_sede()
    {
        $estudiante1 = Estudiante::factory()->create([
            'grupo_id' => $this->grupo->id,
        ]);

        $otraSede = Sede::factory()->create(['institucion_id' => $this->institucion->id]);
        $otroGrupo = Grupo::factory()->create([
            'sede_id' => $otraSede->id,
            'grado_id' => $this->grado->id,
            'anio_id' => $this->anio->id,
        ]);

        $estudiante2 = Estudiante::factory()->create([
            'grupo_id' => $otroGrupo->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/estudiantes?sede_id={$this->sede->id}");

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals($this->sede->id, $data[0]['grupo']['sede']['id']);
    }

    /** @test */
    public function puede_obtener_estudiantes_por_grado()
    {
        $estudiante1 = Estudiante::factory()->create([
            'grupo_id' => $this->grupo->id,
        ]);

        $otroGrado = Grado::factory()->create(['institucion_id' => $this->institucion->id]);
        $otroGrupo = Grupo::factory()->create([
            'sede_id' => $this->sede->id,
            'grado_id' => $otroGrado->id,
            'anio_id' => $this->anio->id,
        ]);

        $estudiante2 = Estudiante::factory()->create([
            'grupo_id' => $otroGrupo->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/estudiantes?grado_id={$this->grado->id}");

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals($this->grado->id, $data[0]['grupo']['grado']['id']);
    }

    /** @test */
    public function valida_que_estudiante_tenga_grupo_valido()
    {
        $userData = [
            'nombre' => 'Test',
            'apellido' => 'User',
            'email' => 'test@example.com',
            'tipo_documento' => 'CC',
            'numero_documento' => '87654321',
            'username' => 'test.user',
            'password' => 'password123',
            'institucion_id' => $this->institucion->id,
            'estado' => 'activo',
        ];

        $studentData = [
            'codigo_estudiantil' => 'EST002',
            'fecha_nacimiento' => '2005-01-15',
            'genero' => 'M',
            'direccion' => 'Calle 123 #45-67',
            'telefono' => '3001234567',
            'estado' => 'activo',
            'grupo_id' => 99999, // Grupo inexistente
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/estudiantes', array_merge($userData, $studentData));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['grupo_id']);
    }

    /** @test */
    public function valida_que_grupo_pertenezca_a_sede_valida()
    {
        $otraInstitucion = Institucion::factory()->create();
        $otraSede = Sede::factory()->create(['institucion_id' => $otraInstitucion->id]);
        $otroGrado = Grado::factory()->create(['institucion_id' => $otraInstitucion->id]);

        // Intentar crear un grupo con grado de otra institución debería fallar
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('El grado debe pertenecer a la misma institución de la sede');

        Grupo::factory()->create([
            'sede_id' => $otraSede->id,
            'grado_id' => $this->grado->id, // Grado de otra institución
            'anio_id' => $this->anio->id,
        ]);
    }

    /** @test */
    public function puede_obtener_estadisticas_de_estudiantes()
    {
        // Crear estudiantes en diferentes estados
        Estudiante::factory()->count(5)->create([
            'grupo_id' => $this->grupo->id,
            'estado' => 'activo',
        ]);

        Estudiante::factory()->count(2)->create([
            'grupo_id' => $this->grupo->id,
            'estado' => 'inactivo',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/estudiantes');

        $response->assertStatus(200);

        $data = $response->json('data');
        $activos = collect($data)->where('estado', 'activo')->count();
        $inactivos = collect($data)->where('estado', 'inactivo')->count();

        $this->assertEquals(5, $activos);
        $this->assertEquals(2, $inactivos);
    }
}
