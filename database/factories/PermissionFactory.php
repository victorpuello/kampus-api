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
        static $increment = 1;

        return [
            'nombre' => 'PermisoTest_'.$increment++,
            'descripcion' => fake()->sentence(),
        ];
    }
}
