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
            'anio_id' => Anio::factory(),
        ];
    }
}