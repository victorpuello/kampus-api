<?php

namespace App\Console\Commands;

use App\Models\Institucion;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class GenerateTestUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:generate-test {count=50 : Número de usuarios a generar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera usuarios de prueba para verificar la paginación';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->argument('count');

        $this->info('🚀 Iniciando generación de '.$count.' usuarios de prueba...');

        // Verificar que existan instituciones
        $instituciones = Institucion::all();
        if ($instituciones->isEmpty()) {
            $this->error('❌ No hay instituciones en la base de datos. Ejecuta primero: php artisan db:seed');

            return 1;
        }

        // Verificar que existan roles
        $roles = Role::all();
        if ($roles->isEmpty()) {
            $this->error('❌ No hay roles en la base de datos. Ejecuta primero: php artisan db:seed');

            return 1;
        }

        $this->info('📊 Instituciones disponibles: '.$instituciones->count());
        $this->info('📊 Roles disponibles: '.$roles->count());

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $successCount = 0;
        $errorCount = 0;

        for ($i = 0; $i < $count; $i++) {
            try {
                // Generar datos únicos
                $nombre = $this->getRandomName();
                $apellido = $this->getRandomLastName();
                $email = $this->generateUniqueEmail($nombre, $apellido, $i + 1);
                $username = $this->generateUniqueUsername($nombre, $apellido, $i + 1);

                // Crear usuario
                $user = User::create([
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'email' => $email,
                    'username' => $username,
                    'password' => Hash::make('password123'),
                    'tipo_documento' => $this->getRandomDocumentType(),
                    'numero_documento' => $this->generateUniqueDocumentNumber(),
                    'estado' => $this->getRandomState(),
                    'institucion_id' => $instituciones->random()->id,
                ]);

                // Asignar rol aleatorio
                $randomRole = $roles->random();
                $user->roles()->attach($randomRole->id);

                $successCount++;

            } catch (\Exception $e) {
                $errorCount++;
                $this->error("\n❌ Error al crear usuario ".($i + 1).': '.$e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();

        $this->newLine(2);
        $this->info('🎉 Generación completada!');
        $this->info('✅ Usuarios creados exitosamente: '.$successCount);
        $this->info('❌ Errores: '.$errorCount);
        $this->info('📊 Total procesados: '.($successCount + $errorCount));

        // Mostrar estadísticas
        $this->newLine();
        $this->info('📈 Estadísticas:');
        $this->info('- Total de usuarios en el sistema: '.User::count());
        $this->info('- Usuarios activos: '.User::where('estado', 'activo')->count());
        $this->info('- Usuarios inactivos: '.User::where('estado', 'inactivo')->count());

        return 0;
    }

    /**
     * Obtener un nombre aleatorio
     */
    private function getRandomName(): string
    {
        $nombres = [
            'Ana', 'Carlos', 'María', 'Juan', 'Laura', 'Pedro', 'Sofia', 'Diego', 'Carmen', 'Luis',
            'Isabella', 'Andrés', 'Valentina', 'Miguel', 'Camila', 'Javier', 'Daniela', 'Roberto', 'Natalia', 'Fernando',
            'Gabriela', 'Ricardo', 'Paula', 'Alejandro', 'Monica', 'Eduardo', 'Patricia', 'Hector', 'Adriana', 'Manuel',
            'Claudia', 'Francisco', 'Elena', 'Rafael', 'Beatriz', 'Alberto', 'Lucia', 'Jorge', 'Rosa', 'Victor',
            'Teresa', 'Guillermo', 'Silvia', 'Mario', 'Angela', 'Oscar', 'Martha', 'Raul', 'Diana', 'Enrique',
        ];

        return $nombres[array_rand($nombres)];
    }

    /**
     * Obtener un apellido aleatorio
     */
    private function getRandomLastName(): string
    {
        $apellidos = [
            'García', 'Rodríguez', 'González', 'Fernández', 'López', 'Martínez', 'Sánchez', 'Pérez', 'Gómez', 'Martin',
            'Jiménez', 'Ruiz', 'Hernández', 'Díaz', 'Moreno', 'Muñoz', 'Álvarez', 'Romero', 'Alonso', 'Gutiérrez',
            'Navarro', 'Torres', 'Domínguez', 'Vázquez', 'Ramos', 'Gil', 'Ramírez', 'Serrano', 'Blanco', 'Suárez',
            'Molina', 'Morales', 'Ortega', 'Delgado', 'Castro', 'Ortiz', 'Rubio', 'Marín', 'Sanz', 'Iglesias',
            'Medina', 'Cortés', 'Garrido', 'Castillo', 'Santos', 'Lozano', 'Guerrero', 'Cano', 'Prieto', 'Méndez',
        ];

        return $apellidos[array_rand($apellidos)];
    }

    /**
     * Generar email único
     */
    private function generateUniqueEmail(string $nombre, string $apellido, int $index): string
    {
        $baseEmail = strtolower($nombre).'.'.strtolower($apellido).$index.'@institucion.edu.co';

        // Si el email ya existe, agregar un sufijo
        $counter = 1;
        $email = $baseEmail;

        while (User::where('email', $email)->exists()) {
            $email = strtolower($nombre).'.'.strtolower($apellido).$index.$counter.'@institucion.edu.co';
            $counter++;
        }

        return $email;
    }

    /**
     * Generar username único
     */
    private function generateUniqueUsername(string $nombre, string $apellido, int $index): string
    {
        $baseUsername = strtolower($nombre).strtolower($apellido).$index;

        // Si el username ya existe, agregar un sufijo
        $counter = 1;
        $username = $baseUsername;

        while (User::where('username', $username)->exists()) {
            $username = strtolower($nombre).strtolower($apellido).$index.$counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Generar número de documento único
     */
    private function generateUniqueDocumentNumber(): string
    {
        do {
            $numero = mt_rand(10000000, 99999999);
        } while (User::where('numero_documento', $numero)->exists());

        return (string) $numero;
    }

    /**
     * Obtener tipo de documento aleatorio
     */
    private function getRandomDocumentType(): string
    {
        $tipos = ['CC', 'TI', 'CE', 'PP'];

        return $tipos[array_rand($tipos)];
    }

    /**
     * Obtener estado aleatorio (90% activo, 10% inactivo)
     */
    private function getRandomState(): string
    {
        return (mt_rand(1, 100) <= 90) ? 'activo' : 'inactivo';
    }
}
