<?php

namespace Tests\Unit;

use App\Models\Institucion;
use App\Models\User;
use App\Models\Anio;
use App\Models\Area;
use App\Models\Grado;
use App\Models\Aula;
use App\Models\FranjaHoraria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Clase de prueba para el modelo Institucion.
 *
 * Contiene pruebas unitarias para verificar la creación y las relaciones
 * del modelo Institucion.
 */
class InstitucionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Prueba que una institución puede ser creada correctamente.
     */
    public function test_institucion_can_be_created()
    {
        $institucion = Institucion::factory()->create();

        $this->assertDatabaseHas('instituciones', [
            'id' => $institucion->id,
        ]);
    }

    /**
     * Prueba que una institución puede tener usuarios asociados.
     */
    public function test_institucion_has_users()
    {
        $institucion = Institucion::factory()->create();
        $user = User::factory()->create(['institucion_id' => $institucion->id]);

        $this->assertTrue($institucion->users->contains($user));
    }

    /**
     * Prueba que una institución puede tener años académicos asociados.
     */
    public function test_institucion_has_anios()
    {
        $institucion = Institucion::factory()->create();
        $anio = Anio::factory()->create(['institucion_id' => $institucion->id]);

        $this->assertTrue($institucion->anios->contains($anio));
    }

    /**
     * Prueba que una institución puede tener áreas académicas asociadas.
     */
    public function test_institucion_has_areas()
    {
        $institucion = Institucion::factory()->create();
        $area = Area::factory()->create(['institucion_id' => $institucion->id]);

        $this->assertTrue($institucion->areas->contains($area));
    }

    /**
     * Prueba que una institución puede tener grados académicos asociados.
     */
    public function test_institucion_has_grados()
    {
        $institucion = Institucion::factory()->create();
        $grado = Grado::factory()->create(['institucion_id' => $institucion->id]);

        $this->assertTrue($institucion->grados->contains($grado));
    }

    /**
     * Prueba que una institución puede tener aulas asociadas.
     */
    public function test_institucion_has_aulas()
    {
        $institucion = Institucion::factory()->create();
        $aula = Aula::factory()->create(['institucion_id' => $institucion->id]);

        $this->assertTrue($institucion->aulas->contains($aula));
    }

    /**
     * Prueba que una institución puede tener franjas horarias asociadas.
     */
    public function test_institucion_has_franjas_horarias()
    {
        $institucion = Institucion::factory()->create();
        $franja = FranjaHoraria::factory()->create(['institucion_id' => $institucion->id]);

        $this->assertTrue($institucion->franjasHorarias->contains($franja));
    }
} 