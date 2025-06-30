<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            // Usuarios
            ['nombre' => 'ver_usuarios', 'descripcion' => 'Ver lista de usuarios'],
            ['nombre' => 'crear_usuarios', 'descripcion' => 'Crear nuevos usuarios'],
            ['nombre' => 'editar_usuarios', 'descripcion' => 'Modificar usuarios existentes'],
            ['nombre' => 'eliminar_usuarios', 'descripcion' => 'Eliminar usuarios'],
            
            // Roles
            ['nombre' => 'ver_roles', 'descripcion' => 'Ver lista de roles'],
            ['nombre' => 'crear_roles', 'descripcion' => 'Crear nuevos roles'],
            ['nombre' => 'editar_roles', 'descripcion' => 'Modificar roles existentes'],
            ['nombre' => 'eliminar_roles', 'descripcion' => 'Eliminar roles'],
            
            // Permisos
            ['nombre' => 'ver_permisos', 'descripcion' => 'Ver lista de permisos'],
            ['nombre' => 'asignar_permisos', 'descripcion' => 'Asignar permisos a roles'],
            
            // Estudiantes
            ['nombre' => 'ver_estudiantes', 'descripcion' => 'Ver lista de estudiantes'],
            ['nombre' => 'crear_estudiantes', 'descripcion' => 'Crear nuevos estudiantes'],
            ['nombre' => 'editar_estudiantes', 'descripcion' => 'Modificar estudiantes'],
            ['nombre' => 'eliminar_estudiantes', 'descripcion' => 'Eliminar estudiantes'],
            
            // Docentes
            ['nombre' => 'ver_docentes', 'descripcion' => 'Ver lista de docentes'],
            ['nombre' => 'crear_docentes', 'descripcion' => 'Crear nuevos docentes'],
            ['nombre' => 'editar_docentes', 'descripcion' => 'Modificar docentes'],
            ['nombre' => 'eliminar_docentes', 'descripcion' => 'Eliminar docentes'],
            
            // Notas
            ['nombre' => 'ver_notas', 'descripcion' => 'Ver notas de estudiantes'],
            ['nombre' => 'crear_notas', 'descripcion' => 'Crear nuevas notas'],
            ['nombre' => 'editar_notas', 'descripcion' => 'Modificar notas'],
            ['nombre' => 'eliminar_notas', 'descripcion' => 'Eliminar notas'],
        ];

        foreach ($permisos as $permiso) {
            Permission::create($permiso);
        }
    }
} 