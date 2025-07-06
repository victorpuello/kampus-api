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

            // Acudientes
            ['nombre' => 'ver_acudientes', 'descripcion' => 'Ver lista de acudientes'],
            ['nombre' => 'crear_acudientes', 'descripcion' => 'Crear nuevos acudientes'],
            ['nombre' => 'editar_acudientes', 'descripcion' => 'Modificar acudientes'],
            ['nombre' => 'eliminar_acudientes', 'descripcion' => 'Eliminar acudientes'],

            // Grados
            ['nombre' => 'ver_grados', 'descripcion' => 'Ver lista de grados'],
            ['nombre' => 'crear_grados', 'descripcion' => 'Crear nuevos grados'],
            ['nombre' => 'editar_grados', 'descripcion' => 'Modificar grados'],
            ['nombre' => 'eliminar_grados', 'descripcion' => 'Eliminar grados'],

            // Grupos
            ['nombre' => 'ver_grupos', 'descripcion' => 'Ver lista de grupos'],
            ['nombre' => 'crear_grupos', 'descripcion' => 'Crear nuevos grupos'],
            ['nombre' => 'editar_grupos', 'descripcion' => 'Modificar grupos'],
            ['nombre' => 'eliminar_grupos', 'descripcion' => 'Eliminar grupos'],
            ['nombre' => 'matricular_estudiantes', 'descripcion' => 'Matricular estudiantes en grupos'],

            // Areas
            ['nombre' => 'ver_areas', 'descripcion' => 'Ver lista de áreas'],
            ['nombre' => 'crear_areas', 'descripcion' => 'Crear nuevas áreas'],
            ['nombre' => 'editar_areas', 'descripcion' => 'Modificar áreas'],
            ['nombre' => 'eliminar_areas', 'descripcion' => 'Eliminar áreas'],

            // Asignaturas
            ['nombre' => 'ver_asignaturas', 'descripcion' => 'Ver lista de asignaturas'],
            ['nombre' => 'crear_asignaturas', 'descripcion' => 'Crear nuevas asignaturas'],
            ['nombre' => 'editar_asignaturas', 'descripcion' => 'Modificar asignaturas'],
            ['nombre' => 'eliminar_asignaturas', 'descripcion' => 'Eliminar asignaturas'],

            // Asignaciones
            ['nombre' => 'ver_asignaciones', 'descripcion' => 'Ver lista de asignaciones'],
            ['nombre' => 'crear_asignaciones', 'descripcion' => 'Crear nuevas asignaciones'],
            ['nombre' => 'editar_asignaciones', 'descripcion' => 'Modificar asignaciones'],
            ['nombre' => 'eliminar_asignaciones', 'descripcion' => 'Eliminar asignaciones'],

            // Franjas Horarias
            ['nombre' => 'ver_franjas_horarias', 'descripcion' => 'Ver lista de franjas horarias'],
            ['nombre' => 'crear_franjas_horarias', 'descripcion' => 'Crear nuevas franjas horarias'],
            ['nombre' => 'editar_franjas_horarias', 'descripcion' => 'Modificar franjas horarias'],
            ['nombre' => 'eliminar_franjas_horarias', 'descripcion' => 'Eliminar franjas horarias'],

            // Sedes
            ['nombre' => 'ver_sedes', 'descripcion' => 'Ver lista de sedes'],
            ['nombre' => 'crear_sedes', 'descripcion' => 'Crear nuevas sedes'],
            ['nombre' => 'editar_sedes', 'descripcion' => 'Modificar sedes'],
            ['nombre' => 'eliminar_sedes', 'descripcion' => 'Eliminar sedes'],

            // Años académicos
            ['nombre' => 'ver_anios', 'descripcion' => 'Ver lista de años académicos'],
            ['nombre' => 'crear_anios', 'descripcion' => 'Crear nuevos años académicos'],
            ['nombre' => 'editar_anios', 'descripcion' => 'Modificar años académicos'],
            ['nombre' => 'eliminar_anios', 'descripcion' => 'Eliminar años académicos'],

            // Períodos
            ['nombre' => 'ver_periodos', 'descripcion' => 'Ver lista de períodos'],
            ['nombre' => 'crear_periodos', 'descripcion' => 'Crear nuevos períodos'],
            ['nombre' => 'editar_periodos', 'descripcion' => 'Modificar períodos'],
            ['nombre' => 'eliminar_periodos', 'descripcion' => 'Eliminar períodos'],

            // Notas
            ['nombre' => 'ver_notas', 'descripcion' => 'Ver notas de estudiantes'],
            ['nombre' => 'crear_notas', 'descripcion' => 'Crear nuevas notas'],
            ['nombre' => 'editar_notas', 'descripcion' => 'Modificar notas'],
            ['nombre' => 'eliminar_notas', 'descripcion' => 'Eliminar notas'],

            // Instituciones
            ['nombre' => 'ver_instituciones', 'descripcion' => 'Ver lista de instituciones'],
            ['nombre' => 'crear_instituciones', 'descripcion' => 'Crear nuevas instituciones'],
            ['nombre' => 'editar_instituciones', 'descripcion' => 'Modificar instituciones'],
            ['nombre' => 'eliminar_instituciones', 'descripcion' => 'Eliminar instituciones'],

            // Reportes
            ['nombre' => 'ver_reportes', 'descripcion' => 'Ver reportes del sistema'],
            ['nombre' => 'crear_reportes', 'descripcion' => 'Crear nuevos reportes'],
            ['nombre' => 'editar_reportes', 'descripcion' => 'Modificar reportes'],
            ['nombre' => 'eliminar_reportes', 'descripcion' => 'Eliminar reportes'],
        ];

        foreach ($permisos as $permiso) {
            Permission::updateOrCreate(
                ['nombre' => $permiso['nombre']],
                ['descripcion' => $permiso['descripcion']]
            );
        }
    }
}
