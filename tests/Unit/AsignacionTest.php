<?php

namespace Tests\Unit;

use App\Models\Anio;
use App\Models\Asignacion;
use App\Models\Asignatura;
use App\Models\Docente;
use App\Models\FranjaHoraria;
use App\Models\Grupo;
use App\Models\Periodo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsignacionTest extends TestCase
{
    use RefreshDatabase;

    public function test_relaciones_basicas()
    {
        $docente = Docente::factory()->create();
        $asignatura = Asignatura::factory()->create();
        $grupo = Grupo::factory()->create();
        $franja = FranjaHoraria::factory()->create();
        $anio = Anio::factory()->create();
        $periodo = Periodo::factory()->create();

        $asignacion = Asignacion::factory()->create([
            'docente_id' => $docente->id,
            'asignatura_id' => $asignatura->id,
            'grupo_id' => $grupo->id,
            'franja_horaria_id' => $franja->id,
            'dia_semana' => 'lunes',
            'anio_academico_id' => $anio->id,
            'periodo_id' => $periodo->id,
        ]);

        $this->assertEquals($docente->id, $asignacion->docente->id);
        $this->assertEquals($asignatura->id, $asignacion->asignatura->id);
        $this->assertEquals($grupo->id, $asignacion->grupo->id);
        $this->assertEquals($franja->id, $asignacion->franjaHoraria->id);
        $this->assertEquals($anio->id, $asignacion->anioAcademico->id);
        $this->assertEquals($periodo->id, $asignacion->periodo->id);
    }

    public function test_scope_activas()
    {
        Asignacion::factory()->count(2)->create(['estado' => 'activo']);
        Asignacion::factory()->count(1)->create(['estado' => 'inactivo']);
        $this->assertCount(2, Asignacion::activas()->get());
    }

    public function test_conflicto_docente_y_grupo()
    {
        $docente = Docente::factory()->create();
        $grupo = Grupo::factory()->create();
        $franja = FranjaHoraria::factory()->create();
        $anio = Anio::factory()->create();
        $asignatura = Asignatura::factory()->create();

        $asignacion1 = Asignacion::factory()->create([
            'docente_id' => $docente->id,
            'asignatura_id' => $asignatura->id,
            'grupo_id' => $grupo->id,
            'franja_horaria_id' => $franja->id,
            'dia_semana' => 'martes',
            'anio_academico_id' => $anio->id,
            'estado' => 'activo',
        ]);

        $asignacion2 = new Asignacion([
            'docente_id' => $docente->id,
            'asignatura_id' => $asignatura->id,
            'grupo_id' => $grupo->id,
            'franja_horaria_id' => $franja->id,
            'dia_semana' => 'martes',
            'anio_academico_id' => $anio->id,
            'estado' => 'activo',
        ]);

        $this->assertTrue($asignacion2->tieneConflictoDocente());
        $this->assertTrue($asignacion2->tieneConflictoGrupo());
    }
}
