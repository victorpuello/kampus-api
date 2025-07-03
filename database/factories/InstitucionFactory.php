<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Institucion>
 */
class InstitucionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->unique()->company() . ' Educativa',
            'siglas' => $this->faker->unique()->regexify('[A-Z]{3,5}'),
            'slogan' => $this->faker->sentence(),
            'dane' => $this->faker->numerify('##########'),
            'resolucion_aprobacion' => 'Resolución ' . $this->faker->numberBetween(1000, 9999) . ' de ' . $this->faker->year(),
            'direccion' => $this->faker->address(),
            'telefono' => $this->faker->phoneNumber(),
            'email' => $this->faker->email(),
            'rector' => $this->faker->name(),
            'escudo' => 'escudos/' . $this->faker->image('public/storage/escudos', 200, 200, null, false),
        ];
    }
}