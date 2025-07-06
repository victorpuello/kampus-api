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
                'tipo_documento' => 'CC',
                'numero_documento' => '12345678',
                'codigo' => 'EST-001',
                'fecha_nacimiento' => '2005-03-15',
                'genero' => 'M',
                'direccion' => 'Calle 123 #45-67',
                'telefono' => '3001234567',
            ],
            [
                'nombre' => 'María',
                'apellido' => 'López',
                'username' => 'mlopez',
                'email' => 'mlopez@example.com',
                'tipo_documento' => 'CC',
                'numero_documento' => '87654321',
                'codigo' => 'EST-002',
                'fecha_nacimiento' => '2006-07-22',
                'genero' => 'F',
                'direccion' => 'Carrera 78 #12-34',
                'telefono' => '3002345678',
            ],
            [
                'nombre' => 'Carlos',
                'apellido' => 'Ramírez',
                'username' => 'cramirez',
                'email' => 'cramirez@example.com',
                'tipo_documento' => 'TI',
                'numero_documento' => '987654321',
                'codigo' => 'EST-003',
                'fecha_nacimiento' => '2007-11-08',
                'genero' => 'M',
                'direccion' => 'Avenida 5 #23-45',
                'telefono' => '3003456789',
            ],
            [
                'nombre' => 'Ana',
                'apellido' => 'Torres',
                'username' => 'atorres',
                'email' => 'atorres@example.com',
                'tipo_documento' => 'CC',
                'numero_documento' => '11223344',
                'codigo' => 'EST-004',
                'fecha_nacimiento' => '2005-09-14',
                'genero' => 'F',
                'direccion' => 'Calle 90 #67-89',
                'telefono' => '3004567890',
            ],
            [
                'nombre' => 'Pedro',
                'apellido' => 'Vargas',
                'username' => 'pvargas',
                'email' => 'pvargas@example.com',
                'tipo_documento' => 'CC',
                'numero_documento' => '55667788',
                'codigo' => 'EST-005',
                'fecha_nacimiento' => '2006-01-30',
                'genero' => 'M',
                'direccion' => 'Carrera 15 #34-56',
                'telefono' => '3005678901',
            ],
        ];

        foreach ($estudiantes as $estudianteData) {
            $user = User::create([
                'nombre' => $estudianteData['nombre'],
                'apellido' => $estudianteData['apellido'],
                'username' => $estudianteData['username'],
                'email' => $estudianteData['email'],
                'tipo_documento' => $estudianteData['tipo_documento'],
                'numero_documento' => $estudianteData['numero_documento'],
                'password' => bcrypt('password'),
                'institucion_id' => 1,
                'estado' => 'activo',
            ]);

            $user->roles()->attach(3); // ID 3 = Rol Estudiante

            Estudiante::create([
                'user_id' => $user->id,
                'codigo_estudiantil' => $estudianteData['codigo'],
                'fecha_nacimiento' => $estudianteData['fecha_nacimiento'],
                'genero' => $estudianteData['genero'],
                'direccion' => $estudianteData['direccion'],
                'telefono' => $estudianteData['telefono'],
                'estado' => 'activo',
            ]);
        }
    }
}
