<?php

namespace Tests\Feature;

use App\Models\Grado;
use App\Models\Institucion;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradoEnumTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear permisos necesarios para grados
        $permissions = [
            'ver_grados',
            'crear_grados',
            'editar_grados',
            'eliminar_grados',
            'grados.index',
            'grados.create',
            'grados.show',
            'grados.update',
            'grados.delete',
            'grados.niveles',
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

    /**
     * Prueba que el modelo Grado tiene las constantes del enum definidas correctamente.
     */
    public function test_grado_model_has_enum_constants(): void
    {
        $this->assertEquals('Preescolar', Grado::NIVEL_PREESCOLAR);
        $this->assertEquals('Básica Primaria', Grado::NIVEL_BASICA_PRIMARIA);
        $this->assertEquals('Básica Secundaria', Grado::NIVEL_BASICA_SECUNDARIA);
        $this->assertEquals('Educación Media', Grado::NIVEL_EDUCACION_MEDIA);
    }

    /**
     * Prueba que el método getNivelesDisponibles() devuelve todos los niveles.
     */
    public function test_get_niveles_disponibles_returns_all_levels(): void
    {
        $niveles = Grado::getNivelesDisponibles();

        $this->assertCount(4, $niveles);
        $this->assertContains('Preescolar', $niveles);
        $this->assertContains('Básica Primaria', $niveles);
        $this->assertContains('Básica Secundaria', $niveles);
        $this->assertContains('Educación Media', $niveles);
    }

    /**
     * Prueba que el método isNivelValido() funciona correctamente.
     */
    public function test_is_nivel_valido_validates_correctly(): void
    {
        $this->assertTrue(Grado::isNivelValido('Preescolar'));
        $this->assertTrue(Grado::isNivelValido('Básica Primaria'));
        $this->assertTrue(Grado::isNivelValido('Básica Secundaria'));
        $this->assertTrue(Grado::isNivelValido('Educación Media'));

        $this->assertFalse(Grado::isNivelValido('Nivel Invalido'));
        $this->assertFalse(Grado::isNivelValido(''));
        $this->assertFalse(Grado::isNivelValido('preescolar')); // case sensitive
    }

    /**
     * Prueba que se puede crear un grado con cada nivel del enum.
     */
    public function test_can_create_grado_with_each_enum_level(): void
    {
        $institucion = Institucion::factory()->create();

        foreach (Grado::getNivelesDisponibles() as $nivel) {
            $grado = Grado::create([
                'nombre' => 'Grado Test '.$nivel,
                'nivel' => $nivel,
                'institucion_id' => $institucion->id,
            ]);

            $this->assertInstanceOf(Grado::class, $grado);
            $this->assertEquals($nivel, $grado->nivel);
            $this->assertDatabaseHas('grados', [
                'id' => $grado->id,
                'nivel' => $nivel,
            ]);
        }
    }

    /**
     * Prueba que el factory funciona correctamente con los nuevos valores del enum.
     */
    public function test_factory_works_with_enum_values(): void
    {
        $grado = Grado::factory()->create();

        $this->assertInstanceOf(Grado::class, $grado);
        $this->assertContains($grado->nivel, Grado::getNivelesDisponibles());
    }

    /**
     * Prueba que los métodos específicos del factory funcionan.
     */
    public function test_factory_specific_methods_work(): void
    {
        $gradoPreescolar = Grado::factory()->preescolar()->create();
        $this->assertEquals(Grado::NIVEL_PREESCOLAR, $gradoPreescolar->nivel);

        $gradoPrimaria = Grado::factory()->basicaPrimaria()->create();
        $this->assertEquals(Grado::NIVEL_BASICA_PRIMARIA, $gradoPrimaria->nivel);

        $gradoSecundaria = Grado::factory()->basicaSecundaria()->create();
        $this->assertEquals(Grado::NIVEL_BASICA_SECUNDARIA, $gradoSecundaria->nivel);

        $gradoMedia = Grado::factory()->educacionMedia()->create();
        $this->assertEquals(Grado::NIVEL_EDUCACION_MEDIA, $gradoMedia->nivel);
    }

    /**
     * Prueba que la API devuelve los niveles disponibles correctamente.
     */
    public function test_api_returns_available_levels(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/grados/niveles');

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJsonCount(4, 'data');

        $niveles = $response->json('data');
        $this->assertContains('Preescolar', $niveles);
        $this->assertContains('Básica Primaria', $niveles);
        $this->assertContains('Básica Secundaria', $niveles);
        $this->assertContains('Educación Media', $niveles);
    }

    /**
     * Prueba que se puede crear un grado a través de la API con un nivel válido.
     */
    public function test_can_create_grado_via_api_with_valid_level(): void
    {
        $institucion = Institucion::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/grados', [
                'nombre' => 'Primero A',
                'nivel' => 'Básica Primaria',
                'institucion_id' => $institucion->id,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data'])
            ->assertJson([
                'data' => [
                    'nombre' => 'Primero A',
                    'nivel' => 'Básica Primaria',
                ],
            ]);
    }

    /**
     * Prueba que la API rechaza niveles inválidos.
     */
    public function test_api_rejects_invalid_levels(): void
    {
        $institucion = Institucion::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/grados', [
                'nombre' => 'Grado Test',
                'nivel' => 'Nivel Invalido',
                'institucion_id' => $institucion->id,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nivel']);
    }

    /**
     * Prueba que se puede actualizar un grado con un nivel válido.
     */
    public function test_can_update_grado_with_valid_level(): void
    {
        // Crear un grado que pertenezca a la institución del usuario
        $grado = Grado::factory()->create([
            'institucion_id' => $this->user->institucion_id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/grados/{$grado->id}", [
                'nivel' => 'Educación Media',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'nivel' => 'Educación Media',
                ],
            ]);
    }

    /**
     * Prueba que el seeder crea grados con niveles válidos.
     */
    public function test_seeder_creates_grados_with_valid_levels(): void
    {
        // Crear una institución primero
        $institucion = Institucion::factory()->create();

        // Ejecutar el seeder
        $this->artisan('db:seed', ['--class' => 'GradoSeeder']);

        // Verificar que se crearon grados con niveles válidos
        $grados = Grado::all();
        $this->assertGreaterThan(0, $grados->count());

        foreach ($grados as $grado) {
            $this->assertContains($grado->nivel, Grado::getNivelesDisponibles());
        }
    }

    /**
     * Prueba que se pueden filtrar grados por nivel.
     */
    public function test_can_filter_grados_by_level(): void
    {
        $institucion = Institucion::factory()->create();

        // Crear grados con diferentes niveles
        Grado::factory()->preescolar()->create(['institucion_id' => $institucion->id]);
        Grado::factory()->basicaPrimaria()->create(['institucion_id' => $institucion->id]);
        Grado::factory()->basicaSecundaria()->create(['institucion_id' => $institucion->id]);

        // Filtrar por nivel específico
        $gradosPreescolar = Grado::where('nivel', Grado::NIVEL_PREESCOLAR)->get();
        $this->assertCount(1, $gradosPreescolar);
        $this->assertEquals(Grado::NIVEL_PREESCOLAR, $gradosPreescolar->first()->nivel);
    }
}
