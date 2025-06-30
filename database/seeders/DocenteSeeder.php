<?php

namespace Database\Seeders;

use App\Models\Docente;
use App\Models\User;
use Illuminate\Database\Seeder;

class DocenteSeeder extends Seeder
{
    public function run(): void
    {
        // Crear docentes adicionales
        $docentes = [
            [
                'nombre' => 'Ana',
                'apellido' => 'Martínez',
                'username' => 'amartinez',
                'email' => 'amartinez@example.com',
            ],
            [
                'nombre' => 'Pedro',
                'apellido' => 'Sánchez',
                'username' => 'psanchez',
                'email' => 'psanchez@example.com',
            ],
            [
                'nombre' => 'Laura',
                'apellido' => 'Díaz',
                'username' => 'ldiaz',
                'email' => 'ldiaz@example.com',
            ],
        ];

        foreach ($docentes as $docenteData) {
            $user = User::create([
                'nombre' => $docenteData['nombre'],
                'apellido' => $docenteData['apellido'],
                'username' => $docenteData['username'],
                'email' => $docenteData['email'],
                'password_hash' => bcrypt('password'),
                'institucion_id' => 1,
                'estado' => 'activo',
            ]);

            $user->roles()->attach(2); // ID 2 = Rol Docente

            Docente::create([
                'user_id' => $user->id,
            ]);
        }
    }
} 