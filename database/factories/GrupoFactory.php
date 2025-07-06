<?php

namespace Database\Factories;

use App\Models\Anio;
use App\Models\Grado;
use App\Models\Docente;
use App\Models\Sede;
use Illuminate\Database\Eloquent\Factories\Factory;

class GrupoFactory extends Factory
{
    public function definition(): array
    {
        // Crear una institución primero
        $institucion = \App\Models\Institucion::factory()->create();
        
        // Crear sede y grado de la misma institución
        $sede = Sede::factory()->create(['institucion_id' => $institucion->id]);
        $grado = Grado::factory()->create(['institucion_id' => $institucion->id]);
        
        return [
            'nombre' => fake()->word(),
            'sede_id' => $sede->id,
            'anio_id' => Anio::factory(),
            'grado_id' => $grado->id,
            'director_docente_id' => Docente::factory(),
        ];
    }

    /**
     * Configura el grupo para usar una sede y grado de la misma institución.
     */
    public function paraInstitucion($institucionId): static
    {
        return $this->state(function (array $attributes) use ($institucionId) {
            // Obtener una sede de la institución
            $sede = Sede::where('institucion_id', $institucionId)->first();
            if (!$sede) {
                $sede = Sede::factory()->create(['institucion_id' => $institucionId]);
            }

            // Obtener un grado de la institución
            $grado = Grado::where('institucion_id', $institucionId)->first();
            if (!$grado) {
                $grado = Grado::factory()->create(['institucion_id' => $institucionId]);
            }

            return [
                'sede_id' => $sede->id,
                'grado_id' => $grado->id,
            ];
        });
    }

    /**
     * Configura el grupo para un año académico específico.
     */
    public function paraAnio($anioId): static
    {
        return $this->state(function (array $attributes) use ($anioId) {
            return [
                'anio_id' => $anioId,
            ];
        });
    }
} 