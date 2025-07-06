<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuario administrador o actualizar si ya existe
        $admin = User::firstOrNew(['email' => 'admin@example.com']);
        $admin->nombre = 'Admin';
        $admin->apellido = 'Sistema';
        $admin->username = 'admin';
        $admin->password = '123456';
        $admin->institucion_id = 1;
        $admin->estado = 'activo';
        $admin->save();

        // Asignar rol de administrador
        $adminRole = Role::where('nombre', 'Administrador')->first();
        if ($adminRole && ! $admin->roles()->where('role_id', $adminRole->id)->exists()) {
            $admin->roles()->attach($adminRole->id);
        }

        // Asignar todos los permisos existentes al rol de administrador
        if ($adminRole) {
            $allPermissions = \App\Models\Permission::pluck('id');
            $adminRole->permissions()->sync($allPermissions);
        }

        // Crear usuario docente
        $docente = User::firstOrNew(['email' => 'jperez@example.com']);
        $docente->nombre = 'Juan';
        $docente->apellido = 'Pérez';
        $docente->username = 'jperez';
        $docente->password = 'password';
        $docente->institucion_id = 1;
        $docente->estado = 'activo';
        $docente->save();

        // Asignar rol de docente
        $docenteRole = Role::where('nombre', 'Docente')->first();
        if ($docenteRole && ! $docente->roles()->where('role_id', $docenteRole->id)->exists()) {
            $docente->roles()->attach($docenteRole->id);
        }

        // Crear usuario estudiante
        $estudiante = User::firstOrNew(['email' => 'mgonzalez@example.com']);
        $estudiante->nombre = 'María';
        $estudiante->apellido = 'González';
        $estudiante->username = 'mgonzalez';
        $estudiante->password = 'password';
        $estudiante->institucion_id = 1;
        $estudiante->estado = 'activo';
        $estudiante->save();

        // Asignar rol de estudiante
        $estudianteRole = Role::where('nombre', 'Estudiante')->first();
        if ($estudianteRole && ! $estudiante->roles()->where('role_id', $estudianteRole->id)->exists()) {
            $estudiante->roles()->attach($estudianteRole->id);
        }

        // Crear usuario acudiente
        $acudiente = User::firstOrNew(['email' => 'crodriguez@example.com']);
        $acudiente->nombre = 'Carlos';
        $acudiente->apellido = 'Rodríguez';
        $acudiente->username = 'crodriguez';
        $acudiente->password = 'password';
        $acudiente->institucion_id = 1;
        $acudiente->estado = 'activo';
        $acudiente->save();

        // Asignar rol de acudiente
        $acudienteRole = Role::where('nombre', 'Acudiente')->first();
        if ($acudienteRole && ! $acudiente->roles()->where('role_id', $acudienteRole->id)->exists()) {
            $acudiente->roles()->attach($acudienteRole->id);
        }
    }
}
