<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    public function definition(): array
    {
        static $increment = 1;

        return [
            'nombre' => 'RolTest_'.$increment++,
            'descripcion' => fake()->sentence(),
        ];
    }
}
