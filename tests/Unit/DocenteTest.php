<?php

namespace Tests\Unit;

use App\Models\Docente;
use App\Models\User;
use App\Models\Institucion;
use App\Models\Asignacion;
use App\Models\Asignatura;
use App\Models\Grupo;
use App\Models\Anio;
use App\Models\Area;
use App\Models\Grado;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Clase de prueba para el modelo Docente.
 *
 * Contiene pruebas unitarias para verificar la creación, relaciones y borrado lógico
 * del modelo Docente.
 */
class DocenteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Prueba que un docente puede ser creado correctamente.
     */
    public function test_docente_can_be_created()
    {
        $docente = Docente::factory()->create();

        $this->assertDatabaseHas('docentes', [
            'id' => $docente->id,
        ]);
    }

    /**
     * Prueba que un docente pertenece a un usuario.
     */
    public function test_docente_belongs_to_user()
    {
        $user = User::factory()->create();
        $docente = Docente::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $docente->user->id);
    }

    /**
     * Prueba que un docente puede tener asignaciones.
     */
    public function test_docente_can_have_asignaciones()
    {
        $docente = Docente::factory()->create();
        $anio = Anio::factory()->create();
        $area = Area::factory()->create();
        $grado = Grado::factory()->create();
        $asignatura = Asignatura::factory()->create(['area_id' => $area->id]);
        $grupo = Grupo::factory()->create(['anio_id' => $anio->id, 'grado_id' => $grado->id]);

        $asignacion = Asignacion::factory()->create([
            'docente_id' => $docente->id,
            'asignatura_id' => $asignatura->id,
            'grupo_id' => $grupo->id,
            'anio_id' => $anio->id,
        ]);

        $this->assertTrue($docente->asignaciones->contains($asignacion));
    }

    /**
     * Prueba que un docente puede ser eliminado lógicamente.
     */
    public function test_docente_can_be_soft_deleted()
    {
        $docente = Docente::factory()->create();
        $docente->delete();

        $this->assertSoftDeleted($docente);
    }
} 