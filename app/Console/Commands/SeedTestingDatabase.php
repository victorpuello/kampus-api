<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SeedTestingDatabase extends Command
{
    protected $signature = 'db:seed-testing';

    protected $description = 'Poblar la base de datos de pruebas con datos de ejemplo';

    public function handle()
    {
        $this->info('Poblando la base de datos de pruebas...');

        // Ejecutar las migraciones en la base de datos de pruebas
        Artisan::call('migrate:fresh', [
            '--env' => 'testing',
            '--database' => 'sqlite_testing',
        ]);

        // Ejecutar el seeder de pruebas
        Artisan::call('db:seed', [
            '--class' => 'TestingDatabaseSeeder',
            '--env' => 'testing',
            '--database' => 'sqlite_testing',
        ]);

        $this->info('¡Base de datos de pruebas poblada exitosamente!');
    }
}
