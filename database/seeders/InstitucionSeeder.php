<?php

namespace Database\Seeders;

use App\Models\Institucion;
use Illuminate\Database\Seeder;

class InstitucionSeeder extends Seeder
{
    public function run(): void
    {
        Institucion::create([
            'nombre' => 'Institución Educativa San José',
            'siglas' => 'IESJ',
        ]);

        Institucion::create([
            'nombre' => 'Colegio Santa María',
            'siglas' => 'CSM',
        ]);

        Institucion::create([
            'nombre' => 'Liceo Moderno',
            'siglas' => 'LM',
        ]);
    }
} 