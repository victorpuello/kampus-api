<?php

namespace Database\Factories;

use App\Models\Area;
use App\Models\Asignatura;
use Illuminate\Database\Eloquent\Factories\Factory;

class AsignaturaFactory extends Factory
{
    protected $model = Asignatura::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->unique()->word.' Asignatura',
            'porcentaje_area' => $this->faker->randomFloat(2, 0, 100),
            'area_id' => Area::factory(),
        ];
    }
}
