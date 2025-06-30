<?php

namespace Database\Factories;

use App\Models\Institucion;
use Illuminate\Database\Eloquent\Factories\Factory;

class InstitucionFactory extends Factory
{
    protected $model = Institucion::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->unique()->company,
            'siglas' => $this->faker->unique()->lexify('???'),
        ];
    }
}