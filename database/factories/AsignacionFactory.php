<?php

namespace Database\Factories;

use App\Models\Asignacion;
use App\Models\Docente;
use App\Models\Asignatura;
use App\Models\Grupo;
use App\Models\Anio;
use Illuminate\Database\Eloquent\Factories\Factory;

class AsignacionFactory extends Factory
{
    protected $model = Asignacion::class;

    public function definition(): array
    {
        return [
            'docente_id' => Docente::factory(),
            'asignatura_id' => Asignatura::factory(),
            'grupo_id' => Grupo::factory(),
            'franja_horaria_id' => \App\Models\FranjaHoraria::factory(),
            'dia_semana' => fake()->randomElement(['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado']),
            'anio_academico_id' => Anio::factory(),
            'periodo_id' => \App\Models\Periodo::factory(),
            'estado' => 'activo',
        ];
    }
}