<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            InstitucionesConSedesSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,
            DocenteSeeder::class,
            EstudianteSeeder::class,
            AcudienteSeeder::class,
            GradoSeeder::class,
            AreaAsignaturaSeeder::class,
            GrupoSeeder::class,
            FranjaHorariaSeeder::class,
        ]);
    }
}
