<?php

namespace Database\Seeders;

use App\Models\Grado;
use App\Models\Institucion;
use Illuminate\Database\Seeder;

class GradoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todas las instituciones
        $instituciones = Institucion::all();

        if ($instituciones->isEmpty()) {
            $this->command->warn('No hay instituciones disponibles. Creando grados sin institución.');
            return;
        }

        // Datos de grados por nivel educativo
        $gradosPorNivel = [
            Grado::NIVEL_PREESCOLAR => [
                'Prejardín',
                'Jardín',
                'Transición'
            ],
            Grado::NIVEL_BASICA_PRIMARIA => [
                'Primero',
                'Segundo', 
                'Tercero',
                'Cuarto',
                'Quinto'
            ],
            Grado::NIVEL_BASICA_SECUNDARIA => [
                'Sexto',
                'Séptimo',
                'Octavo',
                'Noveno'
            ],
            Grado::NIVEL_EDUCACION_MEDIA => [
                'Décimo',
                'Undécimo'
            ]
        ];

        // Crear grados para cada institución
        foreach ($instituciones as $institucion) {
            foreach ($gradosPorNivel as $nivel => $nombres) {
                foreach ($nombres as $nombre) {
                    Grado::create([
                        'nombre' => $nombre,
                        'nivel' => $nivel,
                        'institucion_id' => $institucion->id,
                    ]);
                }
            }
        }

        $this->command->info('Grados creados exitosamente para todas las instituciones.');
    }
}
