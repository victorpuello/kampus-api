<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Permission>
 */
class PermissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->randomElement([
                'ver_usuarios',
                'crear_usuarios',
                'editar_usuarios',
                'eliminar_usuarios',
                'ver_roles',
                'crear_roles',
                'editar_roles',
                'eliminar_roles',
                'ver_permisos',
                'asignar_permisos',
                'ver_estudiantes',
                'crear_estudiantes',
                'editar_estudiantes',
                'eliminar_estudiantes',
                'ver_docentes',
                'crear_docentes',
                'editar_docentes',
                'eliminar_docentes',
                'ver_notas',
                'crear_notas',
                'editar_notas',
                'eliminar_notas',
            ]),
            'descripcion' => fake()->sentence(),
        ];
    }
} 