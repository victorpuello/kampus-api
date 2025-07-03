<?php

namespace Database\Factories;

use App\Models\Institucion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sede>
 */
class SedeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'institucion_id' => Institucion::factory(),
            'nombre' => $this->faker->unique()->words(2, true),
            'direccion' => $this->faker->address(),
            'telefono' => $this->faker->phoneNumber(),
        ];
    }
}
