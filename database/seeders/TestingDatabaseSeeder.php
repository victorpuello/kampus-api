<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TestingDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            InstitucionSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,
            DocenteSeeder::class,
            EstudianteSeeder::class,
            AcudienteSeeder::class,
        ]);
    }
} 