<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Estudiante;
use App\Models\Grado;
use App\Models\Grupo;
use App\Models\Sede;
use App\Models\Institucion;
use App\Models\Anio;
use App\Models\Acudiente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class StudentGradoGrupoSedeUnitTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $institucion;
    protected $sede;
    protected $grado;
    protected $grupo;
    protected $anio;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->institucion = Institucion::factory()->create();
        $this->sede = Sede::factory()->create(['institucion_id' => $this->institucion->id]);
        $this->anio = Anio::factory()->create();
        $this->grado = Grado::factory()->create(['institucion_id' => $this->institucion->id]);
        $this->grupo = Grupo::factory()->create([
            'sede_id' => $this->sede->id,
            'grado_id' => $this->grado->id,
            'anio_id' => $this->anio->id
        ]);
    }

    /** @test */
    public function estudiante_puede_tener_usuario()
    {
        $user = User::factory()->create(['institucion_id' => $this->institucion->id]);
        $estudiante = Estudiante::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $estudiante->user);
        $this->assertEquals($user->id, $estudiante->user->id);
    }

    /** @test */
    public function estudiante_puede_tener_grupo()
    {
        $estudiante = Estudiante::factory()->create(['grupo_id' => $this->grupo->id]);

        $this->assertInstanceOf(Grupo::class, $estudiante->grupo);
        $this->assertEquals($this->grupo->id, $estudiante->grupo->id);
    }

    /** @test */
    public function estudiante_puede_tener_acudientes()
    {
        $estudiante = Estudiante::factory()->create(['grupo_id' => $this->grupo->id]);
        $acudiente = Acudiente::factory()->create();
        
        $estudiante->acudientes()->attach($acudiente->id);

        $this->assertCount(1, $estudiante->acudientes);
        $this->assertInstanceOf(Acudiente::class, $estudiante->acudientes->first());
    }

    /** @test */
    public function estudiante_puede_obtener_grado_a_traves_del_grupo()
    {
        $estudiante = Estudiante::factory()->create(['grupo_id' => $this->grupo->id]);

        $this->assertInstanceOf(Grado::class, $estudiante->grado);
        $this->assertEquals($this->grado->id, $estudiante->grado->id);
    }

    /** @test */
    public function estudiante_puede_obtener_sede_a_traves_del_grupo()
    {
        $estudiante = Estudiante::factory()->create(['grupo_id' => $this->grupo->id]);

        $this->assertInstanceOf(Sede::class, $estudiante->sede);
        $this->assertEquals($this->sede->id, $estudiante->sede->id);
    }

    /** @test */
    public function estudiante_puede_obtener_institucion_a_traves_del_grupo()
    {
        $estudiante = Estudiante::factory()->create(['grupo_id' => $this->grupo->id]);

        $this->assertInstanceOf(Institucion::class, $estudiante->institucion);
        $this->assertEquals($this->institucion->id, $estudiante->institucion->id);
    }

    /** @test */
    public function estudiante_puede_obtener_ubicacion_academica()
    {
        $estudiante = Estudiante::factory()->create(['grupo_id' => $this->grupo->id]);

        $ubicacion = $estudiante->ubicacion_academica;
        
        $this->assertStringContainsString($this->sede->nombre, $ubicacion);
        $this->assertStringContainsString($this->grado->nombre, $ubicacion);
        $this->assertStringContainsString($this->grupo->nombre, $ubicacion);
    }

    /** @test */
    public function estudiante_sin_grupo_retorna_sin_asignar()
    {
        $estudiante = Estudiante::factory()->create(['grupo_id' => null]);

        $this->assertEquals('Sin asignar', $estudiante->ubicacion_academica);
    }

    /** @test */
    public function scope_por_grupo_filtra_estudiantes()
    {
        $estudiante1 = Estudiante::factory()->create(['grupo_id' => $this->grupo->id]);
        $otroGrupo = Grupo::factory()->create([
            'sede_id' => $this->sede->id,
            'grado_id' => $this->grado->id,
            'anio_id' => $this->anio->id
        ]);
        $estudiante2 = Estudiante::factory()->create(['grupo_id' => $otroGrupo->id]);

        $estudiantes = Estudiante::porGrupo($this->grupo->id)->get();

        $this->assertCount(1, $estudiantes);
        $this->assertEquals($estudiante1->id, $estudiantes->first()->id);
    }

    /** @test */
    public function scope_por_grado_filtra_estudiantes()
    {
        $estudiante1 = Estudiante::factory()->create(['grupo_id' => $this->grupo->id]);
        $otroGrado = Grado::factory()->create(['institucion_id' => $this->institucion->id]);
        $otroGrupo = Grupo::factory()->create([
            'sede_id' => $this->sede->id,
            'grado_id' => $otroGrado->id,
            'anio_id' => $this->anio->id
        ]);
        $estudiante2 = Estudiante::factory()->create(['grupo_id' => $otroGrupo->id]);

        $estudiantes = Estudiante::porGrado($this->grado->id)->get();

        $this->assertCount(1, $estudiantes);
        $this->assertEquals($estudiante1->id, $estudiantes->first()->id);
    }

    /** @test */
    public function scope_por_sede_filtra_estudiantes()
    {
        $estudiante1 = Estudiante::factory()->create(['grupo_id' => $this->grupo->id]);
        $otraSede = Sede::factory()->create(['institucion_id' => $this->institucion->id]);
        $otroGrupo = Grupo::factory()->create([
            'sede_id' => $otraSede->id,
            'grado_id' => $this->grado->id,
            'anio_id' => $this->anio->id
        ]);
        $estudiante2 = Estudiante::factory()->create(['grupo_id' => $otroGrupo->id]);

        $estudiantes = Estudiante::porSede($this->sede->id)->get();

        $this->assertCount(1, $estudiantes);
        $this->assertEquals($estudiante1->id, $estudiantes->first()->id);
    }

    /** @test */
    public function scope_por_institucion_filtra_estudiantes()
    {
        $estudiante1 = Estudiante::factory()->create(['grupo_id' => $this->grupo->id]);
        $otraInstitucion = Institucion::factory()->create();
        $otraSede = Sede::factory()->create(['institucion_id' => $otraInstitucion->id]);
        $otroGrado = Grado::factory()->create(['institucion_id' => $otraInstitucion->id]);
        $otroGrupo = Grupo::factory()->create([
            'sede_id' => $otraSede->id,
            'grado_id' => $otroGrado->id,
            'anio_id' => $this->anio->id
        ]);
        $estudiante2 = Estudiante::factory()->create(['grupo_id' => $otroGrupo->id]);

        $estudiantes = Estudiante::porInstitucion($this->institucion->id)->get();

        $this->assertCount(1, $estudiantes);
        $this->assertEquals($estudiante1->id, $estudiantes->first()->id);
    }

    /** @test */
    public function grupo_puede_tener_sede()
    {
        $this->assertInstanceOf(Sede::class, $this->grupo->sede);
        $this->assertEquals($this->sede->id, $this->grupo->sede->id);
    }

    /** @test */
    public function grupo_puede_tener_grado()
    {
        $this->assertInstanceOf(Grado::class, $this->grupo->grado);
        $this->assertEquals($this->grado->id, $this->grupo->grado->id);
    }

    /** @test */
    public function grupo_puede_tener_anio()
    {
        $this->assertInstanceOf(Anio::class, $this->grupo->anio);
        $this->assertEquals($this->anio->id, $this->grupo->anio->id);
    }

    /** @test */
    public function grupo_puede_tener_estudiantes()
    {
        $estudiante1 = Estudiante::factory()->create(['grupo_id' => $this->grupo->id]);
        $estudiante2 = Estudiante::factory()->create(['grupo_id' => $this->grupo->id]);

        $this->assertCount(2, $this->grupo->estudiantes);
        $this->assertInstanceOf(Estudiante::class, $this->grupo->estudiantes->first());
    }

    /** @test */
    public function sede_puede_tener_institucion()
    {
        $this->assertInstanceOf(Institucion::class, $this->sede->institucion);
        $this->assertEquals($this->institucion->id, $this->sede->institucion->id);
    }

    /** @test */
    public function sede_puede_tener_grupos()
    {
        $grupo1 = Grupo::factory()->create([
            'sede_id' => $this->sede->id,
            'grado_id' => $this->grado->id,
            'anio_id' => $this->anio->id
        ]);
        $grupo2 = Grupo::factory()->create([
            'sede_id' => $this->sede->id,
            'grado_id' => $this->grado->id,
            'anio_id' => $this->anio->id
        ]);

        $this->assertCount(3, $this->sede->grupos); // Incluye el grupo del setUp
        $this->assertInstanceOf(Grupo::class, $this->sede->grupos->first());
    }

    /** @test */
    public function grado_puede_tener_institucion()
    {
        $this->assertInstanceOf(Institucion::class, $this->grado->institucion);
        $this->assertEquals($this->institucion->id, $this->grado->institucion->id);
    }

    /** @test */
    public function grado_puede_tener_grupos()
    {
        $grupo1 = Grupo::factory()->create([
            'sede_id' => $this->sede->id,
            'grado_id' => $this->grado->id,
            'anio_id' => $this->anio->id
        ]);
        $grupo2 = Grupo::factory()->create([
            'sede_id' => $this->sede->id,
            'grado_id' => $this->grado->id,
            'anio_id' => $this->anio->id
        ]);

        $this->assertCount(3, $this->grado->grupos); // Incluye el grupo del setUp
        $this->assertInstanceOf(Grupo::class, $this->grado->grupos->first());
    }

    /** @test */
    public function grado_puede_tener_estudiantes_a_traves_de_grupos()
    {
        $estudiante1 = Estudiante::factory()->create(['grupo_id' => $this->grupo->id]);
        $estudiante2 = Estudiante::factory()->create(['grupo_id' => $this->grupo->id]);

        $estudiantes = $this->grado->estudiantes;

        $this->assertCount(2, $estudiantes);
        $this->assertInstanceOf(Estudiante::class, $estudiantes->first());
    }

    /** @test */
    public function institucion_puede_tener_sedes()
    {
        $sede1 = Sede::factory()->create(['institucion_id' => $this->institucion->id]);
        $sede2 = Sede::factory()->create(['institucion_id' => $this->institucion->id]);

        $this->assertCount(3, $this->institucion->sedes); // Incluye la sede del setUp
        $this->assertInstanceOf(Sede::class, $this->institucion->sedes->first());
    }

    /** @test */
    public function institucion_puede_tener_grados()
    {
        $grado1 = Grado::factory()->create(['institucion_id' => $this->institucion->id]);
        $grado2 = Grado::factory()->create(['institucion_id' => $this->institucion->id]);

        $this->assertCount(3, $this->institucion->grados); // Incluye el grado del setUp
        $this->assertInstanceOf(Grado::class, $this->institucion->grados->first());
    }

    /** @test */
    public function institucion_puede_tener_estudiantes_a_traves_de_relaciones()
    {
        $estudiante1 = Estudiante::factory()->create(['grupo_id' => $this->grupo->id]);
        $estudiante2 = Estudiante::factory()->create(['grupo_id' => $this->grupo->id]);

        $estudiantes = $this->institucion->estudiantes;

        $this->assertCount(2, $estudiantes);
        $this->assertInstanceOf(Estudiante::class, $estudiantes->first());
    }

    /** @test */
    public function estudiante_puede_ser_soft_deleted()
    {
        $estudiante = Estudiante::factory()->create(['grupo_id' => $this->grupo->id]);
        
        $estudiante->delete();

        $this->assertSoftDeleted('estudiantes', ['id' => $estudiante->id]);
        $this->assertDatabaseHas('estudiantes', ['id' => $estudiante->id]);
    }

    /** @test */
    public function estudiante_puede_ser_restaurado()
    {
        $estudiante = Estudiante::factory()->create(['grupo_id' => $this->grupo->id]);
        
        $estudiante->delete();
        $estudiante->restore();

        $this->assertDatabaseHas('estudiantes', [
            'id' => $estudiante->id,
            'deleted_at' => null
        ]);
    }

    /** @test */
    public function grado_tiene_nivel_enum_valido()
    {
        $grado = Grado::factory()->create([
            'nivel' => 'Básica Primaria',
            'institucion_id' => $this->institucion->id
        ]);

        $this->assertEquals('Básica Primaria', $grado->nivel);
        $this->assertContains($grado->nivel, ['Preescolar', 'Básica Primaria', 'Básica Secundaria', 'Educación Media']);
    }

    /** @test */
    public function estudiante_tiene_estado_valido()
    {
        $estudiante = Estudiante::factory()->create([
            'estado' => 'activo',
            'grupo_id' => $this->grupo->id
        ]);

        $this->assertEquals('activo', $estudiante->estado);
        $this->assertContains($estudiante->estado, ['activo', 'inactivo']);
    }
} 