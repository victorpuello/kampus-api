<?php

namespace Database\Factories;

use App\Models\Anio;
use Illuminate\Database\Eloquent\Factories\Factory;

class PeriodoFactory extends Factory
{
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-1 year', '+1 year');
        $end = (clone $start)->modify('+2 months');

        return [
            'nombre' => $this->faker->word(),
            'anio_id' => Anio::factory(),
            'fecha_inicio' => $start->format('Y-m-d'),
            'fecha_fin' => $end->format('Y-m-d'),
        ];
    }
}
