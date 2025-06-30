<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'nombre' => 'Administrador',
                'descripcion' => 'Acceso total al sistema',
            ],
            [
                'nombre' => 'Docente',
                'descripcion' => 'Gestiona sus clases y estudiantes',
            ],
            [
                'nombre' => 'Estudiante',
                'descripcion' => 'Acceso a sus notas y actividades',
            ],
            [
                'nombre' => 'Acudiente',
                'descripcion' => 'Seguimiento de estudiantes a cargo',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
} 