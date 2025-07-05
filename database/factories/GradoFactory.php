<?php

namespace Database\Factories;

use App\Models\Grado;
use App\Models\Institucion;
use Illuminate\Database\Eloquent\Factories\Factory;

class GradoFactory extends Factory
{
    protected $model = Grado::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->unique()->word . ' Grado',
            'nivel' => $this->faker->randomElement(Grado::getNivelesDisponibles()),
            'institucion_id' => Institucion::factory(),
        ];
    }

    /**
     * Indica que el grado es de preescolar.
     */
    public function preescolar(): static
    {
        return $this->state(fn (array $attributes) => [
            'nivel' => Grado::NIVEL_PREESCOLAR,
        ]);
    }

    /**
     * Indica que el grado es de básica primaria.
     */
    public function basicaPrimaria(): static
    {
        return $this->state(fn (array $attributes) => [
            'nivel' => Grado::NIVEL_BASICA_PRIMARIA,
        ]);
    }

    /**
     * Indica que el grado es de básica secundaria.
     */
    public function basicaSecundaria(): static
    {
        return $this->state(fn (array $attributes) => [
            'nivel' => Grado::NIVEL_BASICA_SECUNDARIA,
        ]);
    }

    /**
     * Indica que el grado es de educación media.
     */
    public function educacionMedia(): static
    {
        return $this->state(fn (array $attributes) => [
            'nivel' => Grado::NIVEL_EDUCACION_MEDIA,
        ]);
    }
}