<?php

namespace Database\Factories;

use App\Models\Anio;
use Illuminate\Database\Eloquent\Factories\Factory;

class PeriodoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => fake()->word(),
            'anio_id' => Anio::factory(),
        ];
    }
} 