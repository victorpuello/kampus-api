<?php

namespace Database\Seeders;

use App\Models\Estudiante;
use App\Models\User;
use Illuminate\Database\Seeder;

class EstudianteSeeder extends Seeder
{
    public function run(): void
    {
        // Crear estudiantes adicionales
        $estudiantes = [
            [
                'nombre' => 'Juan',
                'apellido' => 'Gómez',
                'username' => 'jgomez',
                'email' => 'jgomez@example.com',
                'codigo' => 'EST-001',
            ],
            [
                'nombre' => 'María',
                'apellido' => 'López',
                'username' => 'mlopez',
                'email' => 'mlopez@example.com',
                'codigo' => 'EST-002',
            ],
            [
                'nombre' => 'Carlos',
                'apellido' => 'Ramírez',
                'username' => 'cramirez',
                'email' => 'cramirez@example.com',
                'codigo' => 'EST-003',
            ],
            [
                'nombre' => 'Ana',
                'apellido' => 'Torres',
                'username' => 'atorres',
                'email' => 'atorres@example.com',
                'codigo' => 'EST-004',
            ],
            [
                'nombre' => 'Pedro',
                'apellido' => 'Vargas',
                'username' => 'pvargas',
                'email' => 'pvargas@example.com',
                'codigo' => 'EST-005',
            ],
        ];

        foreach ($estudiantes as $estudianteData) {
            $user = User::create([
                'nombre' => $estudianteData['nombre'],
                'apellido' => $estudianteData['apellido'],
                'username' => $estudianteData['username'],
                'email' => $estudianteData['email'],
                'password' => bcrypt('password'),
                'institucion_id' => 1,
                'estado' => 'activo',
            ]);

            $user->roles()->attach(3); // ID 3 = Rol Estudiante

            Estudiante::create([
                'user_id' => $user->id,
                'codigo_estudiantil' => $estudianteData['codigo'],
            ]);
        }
    }
} 