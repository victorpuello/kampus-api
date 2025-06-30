<?php

namespace Database\Seeders;

use App\Models\Acudiente;
use App\Models\User;
use App\Models\Estudiante;
use App\Models\Institucion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AcudienteSeeder extends Seeder
{
    public function run(): void
    {
        // Buscar o crear una institución demo
        $institucion = Institucion::first();
        if (!$institucion) {
            $institucion = Institucion::create([
                'nombre' => 'Institución Demo',
                'direccion' => 'Calle Falsa 123',
                'telefono' => '3000000000',
                'email' => 'demo@institucion.com',
                'estado' => 'activo',
            ]);
        }

        // Eliminar usuarios existentes y sus registros relacionados
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::whereIn('username', ['jperez', 'mgonzalez'])->forceDelete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Usuario y acudiente 1
        $user1 = User::create([
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'username' => 'jperez',
            'email' => 'juan.perez@example.com',
            'password' => Hash::make('password123'),
            'estado' => 'activo',
            'institucion_id' => $institucion->id
        ]);

        Acudiente::create([
            'user_id' => $user1->id,
            'nombre' => 'Juan Pérez',
            'telefono' => '3001234567',
            'email' => 'juan.perez@example.com',
        ]);

        // Usuario y acudiente 2
        $user2 = User::create([
            'nombre' => 'María',
            'apellido' => 'González',
            'username' => 'mgonzalez',
            'email' => 'maria.gonzalez@example.com',
            'password' => Hash::make('password123'),
            'estado' => 'activo',
            'institucion_id' => $institucion->id
        ]);

        Acudiente::create([
            'user_id' => $user2->id,
            'nombre' => 'María González',
            'telefono' => '3007654321',
            'email' => 'maria.gonzalez@example.com',
        ]);

        // Crear acudientes adicionales
        $acudientes = [
            [
                'nombre' => 'Roberto',
                'apellido' => 'Gómez',
                'username' => 'rgomez',
                'email' => 'rgomez@example.com',
                'telefono' => '3001234567',
                'estudiante_id' => 1, // ID del estudiante Juan Gómez
            ],
            [
                'nombre' => 'Carmen',
                'apellido' => 'López',
                'username' => 'clopez',
                'email' => 'clopez@example.com',
                'telefono' => '3002345678',
                'estudiante_id' => 2, // ID del estudiante María López
            ],
            [
                'nombre' => 'Alberto',
                'apellido' => 'Ramírez',
                'username' => 'aramirez',
                'email' => 'aramirez@example.com',
                'telefono' => '3003456789',
                'estudiante_id' => 3, // ID del estudiante Carlos Ramírez
            ],
        ];

        foreach ($acudientes as $acudienteData) {
            $user = User::create([
                'nombre' => $acudienteData['nombre'],
                'apellido' => $acudienteData['apellido'],
                'username' => $acudienteData['username'],
                'email' => $acudienteData['email'],
                'password' => bcrypt('password'),
                'institucion_id' => $institucion->id,
                'estado' => 'activo',
            ]);

            $user->roles()->attach(4); // ID 4 = Rol Acudiente

            $acudiente = Acudiente::create([
                'user_id' => $user->id,
                'nombre' => $acudienteData['nombre'] . ' ' . $acudienteData['apellido'],
                'telefono' => $acudienteData['telefono'],
                'email' => $acudienteData['email'],
            ]);

            // Asociar acudiente con estudiante
            $estudiante = Estudiante::find($acudienteData['estudiante_id']);
            if ($estudiante) {
                $estudiante->acudientes()->attach($acudiente->id, ['parentesco' => 'Padre']);
            }
        }
    }
} 