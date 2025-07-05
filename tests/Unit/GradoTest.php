<?php

namespace Tests\Unit;

use App\Models\Grado;
use App\Models\Institucion;
use App\Models\Grupo;
use App\Models\Sede;
use App\Models\Estudiante;
use App\Models\Anio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear datos de prueba
        $this->institucion = Institucion::factory()->create([
            'nombre' => 'Instituto de Prueba'
        ]);
        
        $this->sede = Sede::factory()->create([
            'institucion_id' => $this->institucion->id
        ]);
        
        $this->anio = Anio::factory()->create([
            'institucion_id' => $this->institucion->id
        ]);
    }

    /** @test */
    public function puede_crear_un_grado()
    {
        $grado = Grado::factory()->create([
            'nombre' => 'Grado 1º',
            'nivel' => Grado::NIVEL_BASICA_PRIMARIA,
            'institucion_id' => $this->institucion->id
        ]);

        $this->assertDatabaseHas('grados', [
            'id' => $grado->id,
            'nombre' => 'Grado 1º',
            'nivel' => Grado::NIVEL_BASICA_PRIMARIA,
            'institucion_id' => $this->institucion->id
        ]);
    }

    /** @test */
    public function puede_obtener_la_institucion_asociada()
    {
        $grado = Grado::factory()->create([
            'institucion_id' => $this->institucion->id
        ]);

        $this->assertInstanceOf(Institucion::class, $grado->institucion);
        $this->assertEquals($this->institucion->id, $grado->institucion->id);
    }

    /** @test */
    public function puede_obtener_los_grupos_asociados()
    {
        $grado = Grado::factory()->create([
            'institucion_id' => $this->institucion->id
        ]);

        $grupo = Grupo::factory()->create([
            'grado_id' => $grado->id,
            'sede_id' => $this->sede->id,
            'anio_id' => $this->anio->id
        ]);

        $this->assertCount(1, $grado->grupos);
        $this->assertInstanceOf(Grupo::class, $grado->grupos->first());
        $this->assertEquals($grupo->id, $grado->grupos->first()->id);
    }

    /** @test */
    public function puede_obtener_estudiantes_a_traves_de_grupos()
    {
        $grado = Grado::factory()->create([
            'institucion_id' => $this->institucion->id
        ]);

        $grupo = Grupo::factory()->create([
            'grado_id' => $grado->id,
            'sede_id' => $this->sede->id,
            'anio_id' => $this->anio->id
        ]);

        $estudiante = Estudiante::factory()->create([
            'grupo_id' => $grupo->id
        ]);

        $this->assertCount(1, $grado->estudiantes);
        $this->assertInstanceOf(Estudiante::class, $grado->estudiantes->first());
        $this->assertEquals($estudiante->id, $grado->estudiantes->first()->id);
    }

    /** @test */
    public function puede_obtener_niveles_disponibles()
    {
        $niveles = Grado::getNivelesDisponibles();

        $this->assertIsArray($niveles);
        $this->assertContains(Grado::NIVEL_PREESCOLAR, $niveles);
        $this->assertContains(Grado::NIVEL_BASICA_PRIMARIA, $niveles);
        $this->assertContains(Grado::NIVEL_BASICA_SECUNDARIA, $niveles);
        $this->assertContains(Grado::NIVEL_EDUCACION_MEDIA, $niveles);
        $this->assertCount(4, $niveles);
    }

    /** @test */
    public function puede_validar_nivel_educativo()
    {
        $this->assertTrue(Grado::isNivelValido(Grado::NIVEL_PREESCOLAR));
        $this->assertTrue(Grado::isNivelValido(Grado::NIVEL_BASICA_PRIMARIA));
        $this->assertTrue(Grado::isNivelValido(Grado::NIVEL_BASICA_SECUNDARIA));
        $this->assertTrue(Grado::isNivelValido(Grado::NIVEL_EDUCACION_MEDIA));
        
        $this->assertFalse(Grado::isNivelValido('Nivel Invalido'));
        $this->assertFalse(Grado::isNivelValido(''));
        // No probamos null porque el método requiere string
    }

    /** @test */
    public function puede_obtener_grupos_por_anio()
    {
        $grado = Grado::factory()->create([
            'institucion_id' => $this->institucion->id
        ]);

        $grupo1 = Grupo::factory()->create([
            'grado_id' => $grado->id,
            'sede_id' => $this->sede->id,
            'anio_id' => $this->anio->id
        ]);

        $otroAnio = Anio::factory()->create([
            'institucion_id' => $this->institucion->id
        ]);

        $grupo2 = Grupo::factory()->create([
            'grado_id' => $grado->id,
            'sede_id' => $this->sede->id,
            'anio_id' => $otroAnio->id
        ]);

        $gruposPorAnio = $grado->gruposPorAnio($this->anio->id);

        $this->assertCount(1, $gruposPorAnio->get());
        $this->assertEquals($grupo1->id, $gruposPorAnio->first()->id);
    }

    /** @test */
    public function puede_obtener_estadisticas_por_anio()
    {
        $grado = Grado::factory()->create([
            'institucion_id' => $this->institucion->id
        ]);

        $grupo1 = Grupo::factory()->create([
            'grado_id' => $grado->id,
            'sede_id' => $this->sede->id,
            'anio_id' => $this->anio->id
        ]);

        $grupo2 = Grupo::factory()->create([
            'grado_id' => $grado->id,
            'sede_id' => $this->sede->id,
            'anio_id' => $this->anio->id
        ]);

        // Agregar estudiantes a los grupos
        Estudiante::factory()->count(3)->create(['grupo_id' => $grupo1->id]);
        Estudiante::factory()->count(2)->create(['grupo_id' => $grupo2->id]);

        $estadisticas = $grado->estadisticasPorAnio($this->anio->id);

        $this->assertEquals(2, $estadisticas['total_grupos']);
        $this->assertEquals(5, $estadisticas['total_estudiantes']);
        $this->assertEquals(2.5, $estadisticas['promedio_estudiantes_por_grupo']);
        $this->assertEquals(2, $estadisticas['grupos_con_estudiantes']);
        $this->assertEquals(0, $estadisticas['grupos_sin_estudiantes']);
    }

    /** @test */
    public function puede_obtener_total_estudiantes_por_anio()
    {
        $grado = Grado::factory()->create([
            'institucion_id' => $this->institucion->id
        ]);

        $grupo = Grupo::factory()->create([
            'grado_id' => $grado->id,
            'sede_id' => $this->sede->id,
            'anio_id' => $this->anio->id
        ]);

        Estudiante::factory()->count(5)->create(['grupo_id' => $grupo->id]);

        $total = $grado->totalEstudiantesPorAnio($this->anio->id);

        $this->assertEquals(5, $total);
    }

    /** @test */
    public function respeta_restriccion_unica_por_institucion_y_nombre()
    {
        // Crear primer grado
        Grado::factory()->create([
            'nombre' => 'Grado 1º',
            'institucion_id' => $this->institucion->id
        ]);

        // Intentar crear un grado duplicado debería fallar
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Grado::factory()->create([
            'nombre' => 'Grado 1º',
            'institucion_id' => $this->institucion->id
        ]);
    }

    /** @test */
    public function permite_grados_con_mismo_nombre_en_diferentes_instituciones()
    {
        $otraInstitucion = Institucion::factory()->create();

        // Crear grados con el mismo nombre en diferentes instituciones
        $grado1 = Grado::factory()->create([
            'nombre' => 'Grado 1º',
            'institucion_id' => $this->institucion->id
        ]);

        $grado2 = Grado::factory()->create([
            'nombre' => 'Grado 1º',
            'institucion_id' => $otraInstitucion->id
        ]);

        $this->assertDatabaseHas('grados', ['id' => $grado1->id]);
        $this->assertDatabaseHas('grados', ['id' => $grado2->id]);
        $this->assertNotEquals($grado1->id, $grado2->id);
    }

    /** @test */
    public function puede_ser_eliminado_con_soft_delete()
    {
        $grado = Grado::factory()->create([
            'institucion_id' => $this->institucion->id
        ]);

        $grado->delete();

        $this->assertSoftDeleted('grados', ['id' => $grado->id]);
        $this->assertDatabaseHas('grados', ['id' => $grado->id]); // Debe existir con deleted_at
    }

    /** @test */
    public function puede_ser_restaurado_desde_soft_delete()
    {
        $grado = Grado::factory()->create([
            'institucion_id' => $this->institucion->id
        ]);

        $grado->delete();
        $this->assertSoftDeleted('grados', ['id' => $grado->id]);

        $grado->restore();
        $this->assertNotSoftDeleted('grados', ['id' => $grado->id]);
    }
} 