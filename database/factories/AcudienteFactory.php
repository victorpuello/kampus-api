<?php

namespace Database\Factories;

use App\Models\Acudiente;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AcudienteFactory extends Factory
{
    protected $model = Acudiente::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nombre' => $this->faker->name,
            'telefono' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
        ];
    }
}
