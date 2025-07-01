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
                'telefono' => '3001234567',
                'especialidad' => 'Matemáticas',
                'fecha_contratacion' => '2023-01-15',
                'salario' => 3500000,
                'horario_trabajo' => 'Lunes a Viernes 8:00 AM - 4:00 PM',
            ],
            [
                'nombre' => 'Pedro',
                'apellido' => 'Sánchez',
                'username' => 'psanchez',
                'email' => 'psanchez@example.com',
                'telefono' => '3002345678',
                'especialidad' => 'Ciencias',
                'fecha_contratacion' => '2023-02-01',
                'salario' => 3200000,
                'horario_trabajo' => 'Lunes a Viernes 7:30 AM - 3:30 PM',
            ],
            [
                'nombre' => 'Laura',
                'apellido' => 'Díaz',
                'username' => 'ldiaz',
                'email' => 'ldiaz@example.com',
                'telefono' => '3003456789',
                'especialidad' => 'Lenguaje',
                'fecha_contratacion' => '2023-03-10',
                'salario' => 3800000,
                'horario_trabajo' => 'Lunes a Viernes 8:30 AM - 4:30 PM',
            ],
        ];

        foreach ($docentes as $docenteData) {
            $user = User::create([
                'nombre' => $docenteData['nombre'],
                'apellido' => $docenteData['apellido'],
                'username' => $docenteData['username'],
                'email' => $docenteData['email'],
                'password' => bcrypt('password'),
                'institucion_id' => 1,
                'estado' => 'activo',
            ]);

            $user->roles()->attach(2); // ID 2 = Rol Docente

            Docente::create([
                'user_id' => $user->id,
                'telefono' => $docenteData['telefono'],
                'especialidad' => $docenteData['especialidad'],
                'fecha_contratacion' => $docenteData['fecha_contratacion'],
                'salario' => $docenteData['salario'],
                'horario_trabajo' => $docenteData['horario_trabajo'],
            ]);
        }
    }
} 