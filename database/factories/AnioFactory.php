<?php

namespace Database\Factories;

use App\Models\Anio;
use App\Models\Institucion;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnioFactory extends Factory
{
    protected $model = Anio::class;

    public function definition(): array
    {
        $fechaInicio = $this->faker->dateTimeBetween('-1 year', '+1 year');
        $fechaFin = $this->faker->dateTimeBetween($fechaInicio, '+2 years');

        return [
            'nombre' => $this->faker->unique()->year,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'institucion_id' => Institucion::factory(),
            'estado' => $this->faker->randomElement(['activo', 'inactivo']),
        ];
    }
}