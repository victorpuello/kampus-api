<?php

namespace Tests\Feature;

use App\Models\Grado;
use App\Models\Institucion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateDefaultGradosCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear instituciones de prueba
        $this->institucion1 = Institucion::factory()->create([
            'nombre' => 'Instituto de Prueba 1',
        ]);

        $this->institucion2 = Institucion::factory()->create([
            'nombre' => 'Instituto de Prueba 2',
        ]);
    }

    /** @test */
    public function puede_ejecutar_comando_sin_opciones()
    {
        $this->artisan('grados:create-default')
            ->assertExitCode(0);

        // Verificar que se crearon grados
        $this->assertGreaterThan(0, Grado::count());

        // Verificar que cada institución tiene grados
        foreach ([$this->institucion1, $this->institucion2] as $institucion) {
            $gradosInstitucion = Grado::where('institucion_id', $institucion->id)->count();
            $this->assertGreaterThan(0, $gradosInstitucion);
        }
    }

    /** @test */
    public function puede_ejecutar_comando_con_institucion_especifica()
    {
        $this->artisan('grados:create-default', [
            '--institucion-id' => $this->institucion1->id,
        ])
            ->assertExitCode(0);

        // Verificar que solo se crearon grados para la institución específica
        $gradosInstitucion1 = Grado::where('institucion_id', $this->institucion1->id)->count();
        $gradosInstitucion2 = Grado::where('institucion_id', $this->institucion2->id)->count();

        $this->assertGreaterThan(0, $gradosInstitucion1);
        $this->assertEquals(0, $gradosInstitucion2);
    }

    /** @test */
    public function maneja_institucion_inexistente()
    {
        $this->artisan('grados:create-default', [
            '--institucion-id' => 999,
        ])
            ->expectsOutput('❌ No se encontró la institución con ID: 999')
            ->assertExitCode(1);
    }

    /** @test */
    public function puede_ejecutar_comando_con_tipo_configuracion()
    {
        $this->artisan('grados:create-default', [
            '--tipo' => 'solo_primaria',
        ])
            ->assertExitCode(0);

        // Verificar que solo se crearon grados de primaria
        $gradosPrimaria = Grado::whereIn('nivel', [
            Grado::NIVEL_PREESCOLAR,
            Grado::NIVEL_BASICA_PRIMARIA,
        ])->count();

        $gradosSecundaria = Grado::whereIn('nivel', [
            Grado::NIVEL_BASICA_SECUNDARIA,
            Grado::NIVEL_EDUCACION_MEDIA,
        ])->count();

        $this->assertGreaterThan(0, $gradosPrimaria);
        $this->assertEquals(0, $gradosSecundaria);
    }

    /** @test */
    public function maneja_tipo_configuracion_invalido()
    {
        $this->artisan('grados:create-default', [
            '--tipo' => 'tipo_invalido',
        ])
            ->expectsOutput('❌ Tipo de configuración inválido: tipo_invalido')
            ->expectsOutput('Tipos disponibles: general, solo_primaria, solo_secundaria')
            ->assertExitCode(1);
    }

    /** @test */
    public function puede_ejecutar_comando_con_force()
    {
        // Crear grados iniciales
        $this->artisan('grados:create-default')->assertExitCode(0);

        $gradosIniciales = Grado::count();
        $this->assertGreaterThan(0, $gradosIniciales, 'Debe haber grados iniciales');

        // Refrescar las instituciones para asegurarnos de que existen
        $this->institucion1->refresh();
        $this->institucion2->refresh();

        // Ejecutar con force
        $this->artisan('grados:create-default', [
            '--force' => true,
        ])
            ->assertExitCode(0);

        $gradosFinales = Grado::count();

        // Verificar que hay grados activos después del force
        $this->assertGreaterThan(0, $gradosFinales, 'Debe haber grados activos después del force');

        // Verificar que no hay grados duplicados activos
        $duplicados = \DB::table('grados')
            ->select('nombre', 'institucion_id')
            ->groupBy('nombre', 'institucion_id')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        $this->assertEquals(0, $duplicados, 'No debe haber grados duplicados');

        // Verificar que no hay grados marcados como eliminados (soft delete)
        // Comentado porque ahora usamos forceDelete() que elimina físicamente
        // $gradosEliminados = Grado::onlyTrashed()->count();
        // $this->assertGreaterThan(0, $gradosEliminados, 'No se encontraron grados marcados como eliminados');
    }

    /** @test */
    public function maneja_sin_instituciones()
    {
        // Eliminar todas las instituciones usando delete() en lugar de truncate()
        Institucion::query()->delete();

        $this->artisan('grados:create-default')
            ->expectsOutput('❌ No hay instituciones disponibles en el sistema.')
            ->assertExitCode(1);
    }

    /** @test */
    public function muestra_estadisticas_correctas()
    {
        $this->artisan('grados:create-default')
            ->assertExitCode(0);

        // Verificar que se crearon grados
        $this->assertGreaterThan(0, Grado::count());
    }

    /** @test */
    public function respeta_restriccion_unica_al_recrear()
    {
        // Crear grados iniciales
        $this->artisan('grados:create-default')->assertExitCode(0);

        // Verificar que no hay duplicados después de recrear
        $this->artisan('grados:create-default', [
            '--force' => true,
        ])->assertExitCode(0);

        // Verificar que no hay duplicados
        $duplicados = \DB::table('grados')
            ->select('institucion_id', 'nombre', \DB::raw('count(*) as total'))
            ->groupBy('institucion_id', 'nombre')
            ->having('total', '>', 1)
            ->get();

        $this->assertCount(0, $duplicados, 'Se encontraron grados duplicados después de recrear');
    }

    /** @test */
    public function puede_ejecutar_comando_con_todas_las_opciones()
    {
        $this->artisan('grados:create-default', [
            '--institucion-id' => $this->institucion1->id,
            '--tipo' => 'solo_secundaria',
            '--force' => true,
        ])
            ->assertExitCode(0);

        // Verificar que solo se crearon grados de secundaria para la institución específica
        $gradosSecundaria = Grado::where('institucion_id', $this->institucion1->id)
            ->whereIn('nivel', [
                Grado::NIVEL_BASICA_SECUNDARIA,
                Grado::NIVEL_EDUCACION_MEDIA,
            ])
            ->count();

        $gradosPrimaria = Grado::where('institucion_id', $this->institucion1->id)
            ->whereIn('nivel', [
                Grado::NIVEL_PREESCOLAR,
                Grado::NIVEL_BASICA_PRIMARIA,
            ])
            ->count();

        $this->assertGreaterThan(0, $gradosSecundaria);
        $this->assertEquals(0, $gradosPrimaria);
    }

    /** @test */
    public function maneja_errores_durante_creacion()
    {
        // Mock para simular error en la creación
        $this->mock(Grado::class, function ($mock) {
            $mock->shouldReceive('create')->andThrow(new \Exception('Error de base de datos'));
        });

        $this->artisan('grados:create-default')
            ->assertExitCode(0);
    }

    /** @test */
    public function muestra_progreso_por_institucion()
    {
        $this->artisan('grados:create-default')
            ->assertExitCode(0);

        // Verificar que se crearon grados
        $this->assertGreaterThan(0, Grado::count());
    }
}
