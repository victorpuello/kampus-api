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
            'nivel' => $this->faker->numberBetween(1, 11),
            'institucion_id' => Institucion::factory(),
        ];
    }
}