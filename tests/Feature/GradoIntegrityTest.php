<?php

namespace Tests\Feature;

use App\Models\Anio;
use App\Models\Estudiante;
use App\Models\Grado;
use App\Models\Grupo;
use App\Models\Institucion;
use App\Models\Sede;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradoIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear datos de prueba
        $this->institucion1 = Institucion::factory()->create([
            'nombre' => 'Instituto de Prueba 1',
        ]);

        $this->institucion2 = Institucion::factory()->create([
            'nombre' => 'Instituto de Prueba 2',
        ]);

        $this->sede1 = Sede::factory()->create([
            'institucion_id' => $this->institucion1->id,
        ]);

        $this->sede2 = Sede::factory()->create([
            'institucion_id' => $this->institucion2->id,
        ]);

        $this->anio1 = Anio::factory()->create([
            'institucion_id' => $this->institucion1->id,
        ]);

        $this->anio2 = Anio::factory()->create([
            'institucion_id' => $this->institucion2->id,
        ]);
    }

    /** @test */
    public function valida_coherencia_grado_sede_institucion()
    {
        // Crear grado para institución 1
        $grado = Grado::factory()->create([
            'institucion_id' => $this->institucion1->id,
        ]);

        // Crear grupo con sede de la misma institución (debe funcionar)
        $grupo = Grupo::factory()->create([
            'grado_id' => $grado->id,
            'sede_id' => $this->sede1->id,
            'anio_id' => $this->anio1->id,
        ]);

        $this->assertDatabaseHas('grupos', ['id' => $grupo->id]);

        // Intentar crear grupo con sede de diferente institución (debe fallar)
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('El grado debe pertenecer a la misma institución de la sede');

        Grupo::factory()->create([
            'grado_id' => $grado->id,
            'sede_id' => $this->sede2->id, // Sede de institución diferente
            'anio_id' => $this->anio1->id,
        ]);
    }

    /** @test */
    public function valida_restriccion_unica_grado_por_institucion()
    {
        // Crear primer grado
        $grado1 = Grado::factory()->create([
            'nombre' => 'Grado 1º',
            'institucion_id' => $this->institucion1->id,
        ]);

        // Intentar crear grado duplicado en la misma institución (debe fallar)
        $this->expectException(\Illuminate\Database\QueryException::class);

        Grado::factory()->create([
            'nombre' => 'Grado 1º',
            'institucion_id' => $this->institucion1->id,
        ]);
    }

    /** @test */
    public function permite_grados_iguales_en_diferentes_instituciones()
    {
        // Crear grados con el mismo nombre en diferentes instituciones
        $grado1 = Grado::factory()->create([
            'nombre' => 'Grado 1º',
            'institucion_id' => $this->institucion1->id,
        ]);

        $grado2 = Grado::factory()->create([
            'nombre' => 'Grado 1º',
            'institucion_id' => $this->institucion2->id,
        ]);

        $this->assertDatabaseHas('grados', ['id' => $grado1->id]);
        $this->assertDatabaseHas('grados', ['id' => $grado2->id]);
        $this->assertNotEquals($grado1->id, $grado2->id);
    }

    /** @test */
    public function valida_cascada_eliminacion_grado()
    {
        // Crear grado con grupos y estudiantes
        $grado = Grado::factory()->create([
            'institucion_id' => $this->institucion1->id,
        ]);

        $grupo = Grupo::factory()->create([
            'grado_id' => $grado->id,
            'sede_id' => $this->sede1->id,
            'anio_id' => $this->anio1->id,
        ]);

        $estudiante = Estudiante::factory()->create([
            'grupo_id' => $grupo->id,
        ]);

        // Verificar que existen
        $this->assertDatabaseHas('grados', ['id' => $grado->id]);
        $this->assertDatabaseHas('grupos', ['id' => $grupo->id]);
        $this->assertDatabaseHas('estudiantes', ['id' => $estudiante->id]);

        // Eliminar grado (soft delete)
        $grado->delete();

        // Verificar que el grado está soft deleted
        $this->assertSoftDeleted('grados', ['id' => $grado->id]);

        // Verificar que los grupos y estudiantes siguen existiendo
        $this->assertDatabaseHas('grupos', ['id' => $grupo->id]);
        $this->assertDatabaseHas('estudiantes', ['id' => $estudiante->id]);
    }

    /** @test */
    public function valida_integridad_referencial_grupos()
    {
        // Crear grado
        $grado = Grado::factory()->create([
            'institucion_id' => $this->institucion1->id,
        ]);

        // Crear grupo válido
        $grupo = Grupo::factory()->create([
            'grado_id' => $grado->id,
            'sede_id' => $this->sede1->id,
            'anio_id' => $this->anio1->id,
        ]);

        // Verificar que el grupo puede acceder a su grado
        $this->assertEquals($grado->id, $grupo->grado->id);
        $this->assertEquals($this->institucion1->id, $grupo->grado->institucion_id);

        // Verificar que el grupo puede acceder a su sede
        $this->assertEquals($this->sede1->id, $grupo->sede->id);
        $this->assertEquals($this->institucion1->id, $grupo->sede->institucion_id);

        // Verificar que el grupo puede acceder a su institución
        $this->assertEquals($this->institucion1->id, $grupo->institucion->id);
    }

    /** @test */
    public function valida_estadisticas_grado_con_grupos()
    {
        // Crear grado
        $grado = Grado::factory()->create([
            'institucion_id' => $this->institucion1->id,
        ]);

        // Crear grupos para el grado
        $grupo1 = Grupo::factory()->create([
            'grado_id' => $grado->id,
            'sede_id' => $this->sede1->id,
            'anio_id' => $this->anio1->id,
        ]);

        $grupo2 = Grupo::factory()->create([
            'grado_id' => $grado->id,
            'sede_id' => $this->sede1->id,
            'anio_id' => $this->anio1->id,
        ]);

        // Agregar estudiantes a los grupos
        Estudiante::factory()->count(3)->create(['grupo_id' => $grupo1->id]);
        Estudiante::factory()->count(2)->create(['grupo_id' => $grupo2->id]);

        // Obtener estadísticas
        $estadisticas = $grado->estadisticasPorAnio($this->anio1->id);

        // Verificar estadísticas
        $this->assertEquals(2, $estadisticas['total_grupos']);
        $this->assertEquals(5, $estadisticas['total_estudiantes']);
        $this->assertEquals(2.5, $estadisticas['promedio_estudiantes_por_grupo']);
        $this->assertEquals(2, $estadisticas['grupos_con_estudiantes']);
        $this->assertEquals(0, $estadisticas['grupos_sin_estudiantes']);
    }

    /** @test */
    public function valida_estadisticas_grado_sin_grupos()
    {
        // Crear grado sin grupos
        $grado = Grado::factory()->create([
            'institucion_id' => $this->institucion1->id,
        ]);

        // Obtener estadísticas
        $estadisticas = $grado->estadisticasPorAnio($this->anio1->id);

        // Verificar estadísticas para grado sin grupos
        $this->assertEquals(0, $estadisticas['total_grupos']);
        $this->assertEquals(0, $estadisticas['total_estudiantes']);
        $this->assertEquals(0, $estadisticas['promedio_estudiantes_por_grupo']);
        $this->assertEquals(0, $estadisticas['grupos_con_estudiantes']);
        $this->assertEquals(0, $estadisticas['grupos_sin_estudiantes']);
    }

    /** @test */
    public function valida_niveles_educativos_validos()
    {
        $nivelesValidos = Grado::getNivelesDisponibles();

        // Verificar que todos los niveles son válidos
        foreach ($nivelesValidos as $nivel) {
            $this->assertTrue(Grado::isNivelValido($nivel));
        }

        // Verificar que niveles inválidos no son aceptados
        $nivelesInvalidos = ['Nivel Invalido', '', 'Primaria', 'Secundaria'];

        foreach ($nivelesInvalidos as $nivel) {
            $this->assertFalse(Grado::isNivelValido($nivel));
        }

        // Verificar que null no es aceptado
        $this->assertFalse(Grado::isNivelValido(null));
    }

    /** @test */
    public function valida_relaciones_completas_grado()
    {
        // Crear grado
        $grado = Grado::factory()->create([
            'institucion_id' => $this->institucion1->id,
        ]);

        // Verificar relación con institución
        $this->assertInstanceOf(Institucion::class, $grado->institucion);
        $this->assertEquals($this->institucion1->id, $grado->institucion->id);

        // Crear grupo
        $grupo = Grupo::factory()->create([
            'grado_id' => $grado->id,
            'sede_id' => $this->sede1->id,
            'anio_id' => $this->anio1->id,
        ]);

        // Verificar relación con grupos
        $this->assertCount(1, $grado->grupos);
        $this->assertInstanceOf(Grupo::class, $grado->grupos->first());
        $this->assertEquals($grupo->id, $grado->grupos->first()->id);

        // Crear estudiante
        $estudiante = Estudiante::factory()->create([
            'grupo_id' => $grupo->id,
        ]);

        // Verificar relación con estudiantes
        $this->assertCount(1, $grado->estudiantes);
        $this->assertInstanceOf(Estudiante::class, $grado->estudiantes->first());
        $this->assertEquals($estudiante->id, $grado->estudiantes->first()->id);
    }

    /** @test */
    public function valida_no_duplicados_despues_de_operaciones()
    {
        // Crear grados iniciales
        $grado1 = Grado::factory()->create([
            'nombre' => 'Grado 1º',
            'institucion_id' => $this->institucion1->id,
        ]);

        $grado2 = Grado::factory()->create([
            'nombre' => 'Grado 2º',
            'institucion_id' => $this->institucion1->id,
        ]);

        // Verificar que no hay duplicados
        $duplicados = \DB::table('grados')
            ->select('institucion_id', 'nombre', \DB::raw('count(*) as total'))
            ->groupBy('institucion_id', 'nombre')
            ->having('total', '>', 1)
            ->get();

        $this->assertCount(0, $duplicados, 'Se encontraron grados duplicados');

        // Actualizar un grado
        $grado1->update(['nombre' => 'Grado 1º Actualizado']);

        // Verificar que no hay duplicados después de la actualización
        $duplicadosDespues = \DB::table('grados')
            ->select('institucion_id', 'nombre', \DB::raw('count(*) as total'))
            ->groupBy('institucion_id', 'nombre')
            ->having('total', '>', 1)
            ->get();

        $this->assertCount(0, $duplicadosDespues, 'Se encontraron grados duplicados después de actualización');
    }

    /** @test */
    public function valida_consistencia_datos_grado()
    {
        // Crear grado
        $grado = Grado::factory()->create([
            'nombre' => 'Grado 1º',
            'nivel' => Grado::NIVEL_BASICA_PRIMARIA,
            'institucion_id' => $this->institucion1->id,
        ]);

        // Verificar que los datos son consistentes
        $this->assertNotNull($grado->nombre);
        $this->assertNotNull($grado->nivel);
        $this->assertNotNull($grado->institucion_id);
        $this->assertTrue(Grado::isNivelValido($grado->nivel));
        $this->assertDatabaseHas('instituciones', ['id' => $grado->institucion_id]);

        // Verificar que el nombre no está vacío
        $this->assertNotEmpty($grado->nombre);
        $this->assertNotEmpty($grado->nivel);

        // Verificar que el nivel es uno de los válidos
        $this->assertContains($grado->nivel, Grado::getNivelesDisponibles());
    }
}
