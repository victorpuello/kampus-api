<?php

namespace Tests\Feature;

use App\Models\Grado;
use App\Models\Institucion;
use App\Models\Sede;
use App\Models\Anio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class CreateDefaultGradosCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear instituciones de prueba
        $this->institucion1 = Institucion::factory()->create([
            'nombre' => 'Instituto de Prueba 1'
        ]);
        
        $this->institucion2 = Institucion::factory()->create([
            'nombre' => 'Instituto de Prueba 2'
        ]);
    }

    /** @test */
    public function puede_ejecutar_comando_sin_opciones()
    {
        $this->artisan('grados:create-default')
            ->expectsOutput('🚀 Iniciando creación de grados por defecto...')
            ->expectsOutput('📊 Total de instituciones a procesar: 2')
            ->expectsOutput('⚙️  Tipo de configuración: general')
            ->expectsOutput('🎉 ¡Proceso completado exitosamente!')
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
            '--institucion-id' => $this->institucion1->id
        ])
        ->expectsOutput('🚀 Iniciando creación de grados por defecto...')
        ->expectsOutput('📊 Total de instituciones a procesar: 1')
        ->expectsOutput('⚙️  Tipo de configuración: general')
        ->expectsOutput('🎉 ¡Proceso completado exitosamente!')
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
            '--institucion-id' => 999
        ])
        ->expectsOutput('❌ No se encontró la institución con ID: 999')
        ->assertExitCode(1);
    }

    /** @test */
    public function puede_ejecutar_comando_con_tipo_configuracion()
    {
        $this->artisan('grados:create-default', [
            '--tipo' => 'solo_primaria'
        ])
        ->expectsOutput('🚀 Iniciando creación de grados por defecto...')
        ->expectsOutput('⚙️  Tipo de configuración: solo_primaria')
        ->expectsOutput('🎉 ¡Proceso completado exitosamente!')
        ->assertExitCode(0);

        // Verificar que solo se crearon grados de primaria
        $gradosPrimaria = Grado::whereIn('nivel', [
            Grado::NIVEL_PREESCOLAR,
            Grado::NIVEL_BASICA_PRIMARIA
        ])->count();
        
        $gradosSecundaria = Grado::whereIn('nivel', [
            Grado::NIVEL_BASICA_SECUNDARIA,
            Grado::NIVEL_EDUCACION_MEDIA
        ])->count();

        $this->assertGreaterThan(0, $gradosPrimaria);
        $this->assertEquals(0, $gradosSecundaria);
    }

    /** @test */
    public function maneja_tipo_configuracion_invalido()
    {
        $this->artisan('grados:create-default', [
            '--tipo' => 'tipo_invalido'
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
        
        // Ejecutar con force
        $this->artisan('grados:create-default', [
            '--force' => true
        ])
        ->expectsOutput('🚀 Iniciando creación de grados por defecto...')
        ->expectsOutput('⚠️  Modo FORCE activado - se recrearán grados existentes')
        ->expectsOutput('🎉 ¡Proceso completado exitosamente!')
        ->assertExitCode(0);

        $gradosFinales = Grado::count();
        
        // El número de grados debe ser el mismo (recreados)
        $this->assertEquals($gradosIniciales, $gradosFinales);
    }

    /** @test */
    public function maneja_sin_instituciones()
    {
        // Eliminar todas las instituciones
        Institucion::truncate();
        
        $this->artisan('grados:create-default')
        ->expectsOutput('❌ No hay instituciones disponibles en el sistema.')
        ->assertExitCode(1);
    }

    /** @test */
    public function muestra_estadisticas_correctas()
    {
        $this->artisan('grados:create-default')
        ->expectsOutput('🎉 ¡Proceso completado exitosamente!')
        ->expectsOutput('📊 Resumen final:')
        ->expectsOutput('✅ Grados creados:')
        ->expectsOutput('🔄 Grados recreados:')
        ->expectsOutput('⏭️  Grados existentes:')
        ->expectsOutput('❌ Errores:')
        ->expectsOutput('📚 Total procesados:')
        ->expectsOutput('📋 Configuración aplicada: general')
        ->expectsOutput('📋 Estadísticas actuales por nivel:')
        ->assertExitCode(0);
    }

    /** @test */
    public function respeta_restriccion_unica_al_recrear()
    {
        // Crear grados iniciales
        $this->artisan('grados:create-default')->assertExitCode(0);
        
        // Verificar que no hay duplicados después de recrear
        $this->artisan('grados:create-default', [
            '--force' => true
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
            '--force' => true
        ])
        ->expectsOutput('🚀 Iniciando creación de grados por defecto...')
        ->expectsOutput('📊 Total de instituciones a procesar: 1')
        ->expectsOutput('⚙️  Tipo de configuración: solo_secundaria')
        ->expectsOutput('⚠️  Modo FORCE activado - se recrearán grados existentes')
        ->expectsOutput('🎉 ¡Proceso completado exitosamente!')
        ->assertExitCode(0);

        // Verificar que solo se crearon grados de secundaria para la institución específica
        $gradosSecundaria = Grado::where('institucion_id', $this->institucion1->id)
            ->whereIn('nivel', [
                Grado::NIVEL_BASICA_SECUNDARIA,
                Grado::NIVEL_EDUCACION_MEDIA
            ])
            ->count();
        
        $gradosPrimaria = Grado::where('institucion_id', $this->institucion1->id)
            ->whereIn('nivel', [
                Grado::NIVEL_PREESCOLAR,
                Grado::NIVEL_BASICA_PRIMARIA
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
        ->expectsOutput('❌ Error procesando')
        ->assertExitCode(0);
    }

    /** @test */
    public function muestra_progreso_por_institucion()
    {
        $this->artisan('grados:create-default')
        ->expectsOutput('🏫 Procesando institución:')
        ->expectsOutput('📚 Nivel:')
        ->expectsOutput('✅')
        ->expectsOutput('📊 Institución completada:')
        ->assertExitCode(0);
    }
} 