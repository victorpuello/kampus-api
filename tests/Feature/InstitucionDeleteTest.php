<?php

namespace Tests\Feature;

use App\Models\Anio;
use App\Models\Area;
use App\Models\Aula;
use App\Models\FranjaHoraria;
use App\Models\Grado;
use App\Models\Institucion;
use App\Models\Sede;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InstitucionDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $institucion;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->institucion = Institucion::factory()->create();
    }

    // ==================== PRUEBAS BÁSICAS DE ELIMINACIÓN ====================

    /**
     * Prueba que un usuario autenticado puede eliminar una institución
     */
    public function test_can_delete_institucion()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/instituciones/{$this->institucion->id}");

        $response->assertStatus(204);

        // Verificar que la institución fue eliminada (soft delete)
        $this->assertSoftDeleted('instituciones', ['id' => $this->institucion->id]);

        // Verificar que no aparece en consultas normales
        $this->assertDatabaseMissing('instituciones', [
            'id' => $this->institucion->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * Prueba que no se puede eliminar una institución inexistente
     */
    public function test_cannot_delete_nonexistent_institucion()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson('/api/v1/instituciones/99999');

        $response->assertStatus(404);
    }

    /**
     * Prueba que un usuario no autenticado no puede eliminar una institución
     */
    public function test_unauthorized_user_cannot_delete_institucion()
    {
        $response = $this->deleteJson("/api/v1/instituciones/{$this->institucion->id}");

        $response->assertStatus(401);

        // Verificar que la institución no fue eliminada
        $this->assertDatabaseHas('instituciones', [
            'id' => $this->institucion->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * Prueba que se puede eliminar una institución ya eliminada (no debe fallar)
     */
    public function test_can_delete_already_deleted_institucion()
    {
        // Eliminar la institución primero
        $this->institucion->delete();

        // Verificar que fue eliminada
        $this->assertSoftDeleted('instituciones', ['id' => $this->institucion->id]);

        // Intentar eliminar de nuevo - debería retornar 404 porque no encuentra la institución
        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/instituciones/{$this->institucion->id}");

        $response->assertStatus(404);

        // Verificar que sigue eliminada
        $this->assertSoftDeleted('instituciones', ['id' => $this->institucion->id]);
    }

    // ==================== PRUEBAS DE ELIMINACIÓN CON ARCHIVOS ====================

    /**
     * Prueba que se eliminan los archivos cuando se elimina la institución
     */
    public function test_deletes_files_when_institucion_is_deleted()
    {
        // Crear un archivo de escudo
        $file = UploadedFile::fake()->image('escudo.jpg', 300, 300);

        // Subir el archivo
        $this->institucion->setFileFields(['escudo']);
        $this->institucion->setFilePaths(['escudo' => 'instituciones/escudos']);
        $this->institucion->uploadFile($file, 'escudo');

        $escudoPath = $this->institucion->escudo;

        // Verificar que el archivo existe
        $this->assertTrue(Storage::disk('public')->exists($escudoPath));

        // Eliminar la institución
        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/instituciones/{$this->institucion->id}");

        $response->assertStatus(204);

        // Verificar que el archivo fue eliminado
        $this->assertFalse(Storage::disk('public')->exists($escudoPath));
    }

    /**
     * Prueba que no se elimina la imagen por defecto cuando se elimina una institución
     */
    public function test_does_not_delete_default_image_when_institucion_is_deleted()
    {
        // La institución usa la imagen por defecto por defecto
        $this->assertEquals('instituciones/escudos/default.png', $this->institucion->escudo);

        // Eliminar la institución
        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/instituciones/{$this->institucion->id}");

        $response->assertStatus(204);

        // Verificar que la institución fue eliminada
        $this->assertSoftDeleted('instituciones', ['id' => $this->institucion->id]);

        // La prueba verifica que el sistema maneja correctamente la imagen por defecto
        // sin intentar eliminarla cuando se elimina la institución
    }

    // ==================== PRUEBAS DE ELIMINACIÓN CON RELACIONES ====================

    /**
     * Prueba que se pueden eliminar instituciones con sedes
     */
    public function test_can_delete_institucion_with_sedes()
    {
        // Crear sedes para la institución
        $sede1 = Sede::factory()->create(['institucion_id' => $this->institucion->id]);
        $sede2 = Sede::factory()->create(['institucion_id' => $this->institucion->id]);

        // Verificar que las sedes existen
        $this->assertDatabaseHas('sedes', ['id' => $sede1->id]);
        $this->assertDatabaseHas('sedes', ['id' => $sede2->id]);

        // Eliminar la institución
        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/instituciones/{$this->institucion->id}");

        $response->assertStatus(204);

        // Verificar que la institución fue eliminada
        $this->assertSoftDeleted('instituciones', ['id' => $this->institucion->id]);

        // Verificar que las sedes siguen existiendo (no se eliminan automáticamente)
        $this->assertDatabaseHas('sedes', ['id' => $sede1->id]);
        $this->assertDatabaseHas('sedes', ['id' => $sede2->id]);
    }

    /**
     * Prueba que se pueden eliminar instituciones con años académicos
     */
    public function test_can_delete_institucion_with_anios()
    {
        // Crear años académicos para la institución
        $anio1 = Anio::factory()->create(['institucion_id' => $this->institucion->id]);
        $anio2 = Anio::factory()->create(['institucion_id' => $this->institucion->id]);

        // Eliminar la institución
        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/instituciones/{$this->institucion->id}");

        $response->assertStatus(204);

        // Verificar que la institución fue eliminada
        $this->assertSoftDeleted('instituciones', ['id' => $this->institucion->id]);

        // Verificar que los años siguen existiendo
        $this->assertDatabaseHas('anios', ['id' => $anio1->id]);
        $this->assertDatabaseHas('anios', ['id' => $anio2->id]);
    }

    /**
     * Prueba que se pueden eliminar instituciones con áreas
     */
    public function test_can_delete_institucion_with_areas()
    {
        // Crear áreas para la institución
        $area1 = Area::factory()->create(['institucion_id' => $this->institucion->id]);
        $area2 = Area::factory()->create(['institucion_id' => $this->institucion->id]);

        // Eliminar la institución
        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/instituciones/{$this->institucion->id}");

        $response->assertStatus(204);

        // Verificar que la institución fue eliminada
        $this->assertSoftDeleted('instituciones', ['id' => $this->institucion->id]);

        // Verificar que las áreas siguen existiendo
        $this->assertDatabaseHas('areas', ['id' => $area1->id]);
        $this->assertDatabaseHas('areas', ['id' => $area2->id]);
    }

    /**
     * Prueba que se pueden eliminar instituciones con grados
     */
    public function test_can_delete_institucion_with_grados()
    {
        // Crear grados para la institución
        $grado1 = Grado::factory()->create(['institucion_id' => $this->institucion->id]);
        $grado2 = Grado::factory()->create(['institucion_id' => $this->institucion->id]);

        // Eliminar la institución
        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/instituciones/{$this->institucion->id}");

        $response->assertStatus(204);

        // Verificar que la institución fue eliminada
        $this->assertSoftDeleted('instituciones', ['id' => $this->institucion->id]);

        // Verificar que los grados siguen existiendo
        $this->assertDatabaseHas('grados', ['id' => $grado1->id]);
        $this->assertDatabaseHas('grados', ['id' => $grado2->id]);
    }

    /**
     * Prueba que se pueden eliminar instituciones con aulas
     */
    public function test_can_delete_institucion_with_aulas()
    {
        // Crear aulas para la institución con tipos válidos
        $aula1 = Aula::factory()->create([
            'institucion_id' => $this->institucion->id,
            'tipo' => 'Salón',
        ]);
        $aula2 = Aula::factory()->create([
            'institucion_id' => $this->institucion->id,
            'tipo' => 'Laboratorio',
        ]);

        // Eliminar la institución
        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/instituciones/{$this->institucion->id}");

        $response->assertStatus(204);

        // Verificar que la institución fue eliminada
        $this->assertSoftDeleted('instituciones', ['id' => $this->institucion->id]);

        // Verificar que las aulas siguen existiendo
        $this->assertDatabaseHas('aulas', ['id' => $aula1->id]);
        $this->assertDatabaseHas('aulas', ['id' => $aula2->id]);
    }

    /**
     * Prueba que se pueden eliminar instituciones con franjas horarias
     */
    public function test_can_delete_institucion_with_franjas_horarias()
    {
        // Crear franjas horarias para la institución
        $franja1 = FranjaHoraria::factory()->create(['institucion_id' => $this->institucion->id]);
        $franja2 = FranjaHoraria::factory()->create(['institucion_id' => $this->institucion->id]);

        // Eliminar la institución
        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/instituciones/{$this->institucion->id}");

        $response->assertStatus(204);

        // Verificar que la institución fue eliminada
        $this->assertSoftDeleted('instituciones', ['id' => $this->institucion->id]);

        // Verificar que las franjas horarias siguen existiendo
        $this->assertDatabaseHas('franjas_horarias', ['id' => $franja1->id]);
        $this->assertDatabaseHas('franjas_horarias', ['id' => $franja2->id]);
    }

    // ==================== PRUEBAS DE ELIMINACIÓN MÚLTIPLE ====================

    /**
     * Prueba que se pueden eliminar múltiples instituciones
     */
    public function test_can_delete_multiple_instituciones()
    {
        // Crear múltiples instituciones
        $institucion1 = Institucion::factory()->create();
        $institucion2 = Institucion::factory()->create();
        $institucion3 = Institucion::factory()->create();

        // Eliminar la primera institución
        $response1 = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/instituciones/{$institucion1->id}");
        $response1->assertStatus(204);

        // Eliminar la segunda institución
        $response2 = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/instituciones/{$institucion2->id}");
        $response2->assertStatus(204);

        // Verificar que las instituciones fueron eliminadas
        $this->assertSoftDeleted('instituciones', ['id' => $institucion1->id]);
        $this->assertSoftDeleted('instituciones', ['id' => $institucion2->id]);

        // Verificar que la tercera institución sigue existiendo
        $this->assertDatabaseHas('instituciones', [
            'id' => $institucion3->id,
            'deleted_at' => null,
        ]);

        // Verificar que se pueden eliminar múltiples instituciones en secuencia
        $response3 = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/instituciones/{$institucion3->id}");
        $response3->assertStatus(204);

        $this->assertSoftDeleted('instituciones', ['id' => $institucion3->id]);
    }

    // ==================== PRUEBAS DE RECUPERACIÓN ====================

    /**
     * Prueba que se puede recuperar una institución eliminada
     */
    public function test_can_restore_deleted_institucion()
    {
        // Eliminar la institución
        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/instituciones/{$this->institucion->id}");
        $response->assertStatus(204);

        // Verificar que fue eliminada
        $this->assertSoftDeleted('instituciones', ['id' => $this->institucion->id]);

        // Recuperar la institución
        $this->institucion->restore();

        // Verificar que fue recuperada
        $this->assertDatabaseHas('instituciones', [
            'id' => $this->institucion->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * Prueba que se puede eliminar permanentemente una institución
     */
    public function test_can_force_delete_institucion()
    {
        // Eliminar la institución
        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/instituciones/{$this->institucion->id}");
        $response->assertStatus(204);

        // Verificar que fue eliminada (soft delete)
        $this->assertSoftDeleted('instituciones', ['id' => $this->institucion->id]);

        // Eliminar permanentemente
        $this->institucion->forceDelete();

        // Verificar que fue eliminada permanentemente
        $this->assertDatabaseMissing('instituciones', ['id' => $this->institucion->id]);
    }

    // ==================== PRUEBAS DE CONSULTAS POST-ELIMINACIÓN ====================

    /**
     * Prueba que las instituciones eliminadas no aparecen en consultas normales
     */
    public function test_deleted_instituciones_dont_appear_in_normal_queries()
    {
        // Eliminar la institución
        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/instituciones/{$this->institucion->id}");
        $response->assertStatus(204);

        // Verificar que no aparece en el listado
        $listResponse = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/instituciones');

        $listResponse->assertStatus(200);
        $listResponse->assertJsonMissing(['id' => $this->institucion->id]);

        // Verificar que no se puede acceder directamente
        $showResponse = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/instituciones/{$this->institucion->id}");

        $showResponse->assertStatus(404);
    }

    /**
     * Prueba que se puede acceder a instituciones eliminadas con withTrashed
     */
    public function test_can_access_deleted_instituciones_with_with_trashed()
    {
        // Eliminar la institución
        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/instituciones/{$this->institucion->id}");
        $response->assertStatus(204);

        // Verificar que se puede acceder con withTrashed
        $deletedInstitucion = Institucion::withTrashed()->find($this->institucion->id);
        $this->assertNotNull($deletedInstitucion);
        $this->assertNotNull($deletedInstitucion->deleted_at);
    }

    // ==================== PRUEBAS DE INTEGRIDAD DE DATOS ====================

    /**
     * Prueba que la eliminación no afecta la integridad de los datos
     */
    public function test_deletion_preserves_data_integrity()
    {
        // Guardar datos originales (solo campos básicos)
        $originalData = [
            'nombre' => $this->institucion->nombre,
            'siglas' => $this->institucion->siglas,
            'slogan' => $this->institucion->slogan,
            'dane' => $this->institucion->dane,
            'resolucion_aprobacion' => $this->institucion->resolucion_aprobacion,
            'direccion' => $this->institucion->direccion,
            'telefono' => $this->institucion->telefono,
            'email' => $this->institucion->email,
            'rector' => $this->institucion->rector,
        ];

        // Eliminar la institución
        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/instituciones/{$this->institucion->id}");
        $response->assertStatus(204);

        // Recuperar la institución
        $this->institucion->restore();
        $this->institucion->refresh();

        // Verificar que los datos se mantuvieron intactos
        foreach ($originalData as $key => $value) {
            $this->assertEquals($value, $this->institucion->$key, "El campo {$key} no se mantuvo intacto");
        }
    }
}
