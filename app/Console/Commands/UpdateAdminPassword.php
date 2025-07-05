<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class UpdateAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:update-password {--email=admin@example.com : Email del usuario administrador}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza la contraseña del usuario administrador a 123456';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email');
        
        $this->info("Buscando usuario administrador con email: {$email}");
        
        // Buscar el usuario administrador
        $admin = User::where('email', $email)->first();
        
        if (!$admin) {
            $this->error("No se encontró un usuario con el email: {$email}");
            $this->info("Usuarios disponibles:");
            
            $users = User::select('id', 'nombre', 'apellido', 'email', 'username')->get();
            foreach ($users as $user) {
                $this->line("- ID: {$user->id}, Nombre: {$user->nombre} {$user->apellido}, Email: {$user->email}, Username: {$user->username}");
            }
            
            return 1;
        }
        
        $this->info("Usuario encontrado: {$admin->nombre} {$admin->apellido} ({$admin->email})");
        
        // Actualizar la contraseña
        $admin->password = Hash::make('123456');
        $admin->save();
        
        $this->info("✅ Contraseña actualizada exitosamente a '123456'");
        $this->info("El usuario {$admin->email} ahora puede iniciar sesión con la contraseña: 123456");
        
        return 0;
    }
} 