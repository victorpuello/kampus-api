<?php

namespace Database\Factories;

use App\Models\FranjaHoraria;
use App\Models\Institucion;
use Illuminate\Database\Eloquent\Factories\Factory;

class FranjaHorariaFactory extends Factory
{
    protected $model = FranjaHoraria::class;

    public function definition(): array
    {
        $horaInicio = $this->faker->time('H:i', '12:00');
        $horaFin = date('H:i', strtotime($horaInicio . ' +1 hour'));

        return [
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin,
            'institucion_id' => Institucion::factory(),
        ];
    }
}