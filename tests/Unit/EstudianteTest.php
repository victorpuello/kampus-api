<?php

namespace Tests\Unit;

use App\Models\Estudiante;
use App\Models\User;
use App\Models\Acudiente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Clase de prueba para el modelo Estudiante.
 *
 * Contiene pruebas unitarias para verificar la creación, relaciones y borrado lógico
 * del modelo Estudiante.
 */
class EstudianteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Prueba que un estudiante puede ser creado correctamente.
     */
    public function test_estudiante_can_be_created()
    {
        $estudiante = Estudiante::factory()->create();

        $this->assertDatabaseHas('estudiantes', [
            'id' => $estudiante->id,
        ]);
    }

    /**
     * Prueba que un estudiante pertenece a un usuario.
     */
    public function test_estudiante_belongs_to_user()
    {
        $user = User::factory()->create();
        $estudiante = Estudiante::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $estudiante->user->id);
    }

    /**
     * Prueba que un estudiante puede tener acudientes.
     */
    public function test_estudiante_can_have_acudientes()
    {
        $estudiante = Estudiante::factory()->create();
        $acudiente = Acudiente::factory()->create();

        $estudiante->acudientes()->attach($acudiente->id, ['parentesco' => 'Padre']);

        $this->assertTrue($estudiante->acudientes->contains($acudiente));
    }

    /**
     * Prueba que un estudiante puede ser eliminado lógicamente.
     */
    public function test_estudiante_can_be_soft_deleted()
    {
        $estudiante = Estudiante::factory()->create();
        $estudiante->delete();

        $this->assertSoftDeleted($estudiante);
    }
} 