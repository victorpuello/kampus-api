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
        ];
    }
}