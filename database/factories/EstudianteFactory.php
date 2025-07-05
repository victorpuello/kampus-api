<?php

namespace Database\Factories;

use App\Models\Estudiante;
use App\Models\User;
use App\Models\Grupo;
use Illuminate\Database\Eloquent\Factories\Factory;

class EstudianteFactory extends Factory
{
    protected $model = Estudiante::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'codigo_estudiantil' => $this->faker->unique()->numerify('EST-####'),
            'grupo_id' => null, // Opcional, se puede asignar después
        ];
    }

    /**
     * Configura el estudiante para pertenecer a un grupo específico.
     */
    public function enGrupo($grupoId): static
    {
        return $this->state(function (array $attributes) use ($grupoId) {
            return [
                'grupo_id' => $grupoId,
            ];
        });
    }

    /**
     * Configura el estudiante para pertenecer a un grupo de una institución específica.
     */
    public function enInstitucion($institucionId): static
    {
        return $this->state(function (array $attributes) use ($institucionId) {
            // Buscar un grupo de la institución
            $grupo = Grupo::whereHas('sede', function ($q) use ($institucionId) {
                $q->where('institucion_id', $institucionId);
            })->first();

            if (!$grupo) {
                $grupo = Grupo::factory()->paraInstitucion($institucionId)->create();
            }

            return [
                'grupo_id' => $grupo->id,
            ];
        });
    }
}