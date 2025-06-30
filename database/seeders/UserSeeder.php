<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuario administrador
        $admin = new User([
            'nombre' => 'Admin',
            'apellido' => 'Sistema',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password_hash' => 'password',
            'institucion_id' => 1,
            'estado' => 'activo',
        ]);
        $admin->save();

        // Asignar rol de administrador
        $admin->roles()->attach(Role::where('nombre', 'Administrador')->first());

        // Crear usuario docente
        $docente = new User([
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'username' => 'jperez',
            'email' => 'jperez@example.com',
            'password_hash' => 'password',
            'institucion_id' => 1,
            'estado' => 'activo',
        ]);
        $docente->save();

        // Asignar rol de docente
        $docente->roles()->attach(Role::where('nombre', 'Docente')->first());

        // Crear usuario estudiante
        $estudiante = new User([
            'nombre' => 'María',
            'apellido' => 'González',
            'username' => 'mgonzalez',
            'email' => 'mgonzalez@example.com',
            'password_hash' => 'password',
            'institucion_id' => 1,
            'estado' => 'activo',
        ]);
        $estudiante->save();

        // Asignar rol de estudiante
        $estudiante->roles()->attach(Role::where('nombre', 'Estudiante')->first());

        // Crear usuario acudiente
        $acudiente = new User([
            'nombre' => 'Carlos',
            'apellido' => 'Rodríguez',
            'username' => 'crodriguez',
            'email' => 'crodriguez@example.com',
            'password_hash' => 'password',
            'institucion_id' => 1,
            'estado' => 'activo',
        ]);
        $acudiente->save();

        // Asignar rol de acudiente
        $acudiente->roles()->attach(Role::where('nombre', 'Acudiente')->first());
    }
} 