<?php

namespace Database\Factories;

use App\Models\Anio;
use App\Models\Grado;
use App\Models\Docente;
use Illuminate\Database\Eloquent\Factories\Factory;

class GrupoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => fake()->word(),
            'anio_id' => Anio::factory(),
            'grado_id' => Grado::factory(),
            'director_docente_id' => Docente::factory(),
        ];
    }
} 