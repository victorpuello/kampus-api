<?php

namespace Database\Factories;

use App\Models\Aula;
use App\Models\Institucion;
use Illuminate\Database\Eloquent\Factories\Factory;

class AulaFactory extends Factory
{
    protected $model = Aula::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->unique()->word.' Aula',
            'tipo' => $this->faker->randomElement(['Salón', 'Laboratorio', 'Auditorio', 'Deportivo']),
            'capacidad' => $this->faker->numberBetween(10, 50),
            'institucion_id' => Institucion::factory(),
        ];
    }
}
