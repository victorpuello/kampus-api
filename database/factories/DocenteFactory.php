<?php

namespace Database\Factories;

use App\Models\Docente;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocenteFactory extends Factory
{
    protected $model = Docente::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'telefono' => $this->faker->phoneNumber,
            'especialidad' => $this->faker->randomElement(['Matemáticas', 'Ciencias', 'Lenguaje', 'Historia', 'Inglés', 'Educación Física', 'Arte', 'Música']),
            'fecha_contratacion' => $this->faker->date(),
            'salario' => $this->faker->randomFloat(2, 2000000, 5000000),
            'horario_trabajo' => 'Lunes a Viernes 8:00 AM - 4:00 PM',
        ];
    }
}