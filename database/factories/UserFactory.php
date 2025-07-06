<?php

namespace Database\Factories;

use App\Models\Institucion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->firstName,
            'apellido' => $this->faker->lastName,
            'username' => $this->faker->unique()->userName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
            'institucion_id' => Institucion::factory(),
            'estado' => $this->faker->randomElement(['activo', 'inactivo']),
        ];
    }
}
