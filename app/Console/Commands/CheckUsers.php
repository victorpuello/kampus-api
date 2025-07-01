<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckUsers extends Command
{
    protected $signature = 'users:check';
    protected $description = 'Verifica la cantidad de usuarios en la base de datos';

    public function handle()
    {
        $total = User::count();
        $activos = User::where('estado', 'activo')->count();
        $inactivos = User::where('estado', 'inactivo')->count();

        $this->info("📊 Estadísticas de usuarios:");
        $this->info("- Total de usuarios: " . $total);
        $this->info("- Usuarios activos: " . $activos);
        $this->info("- Usuarios inactivos: " . $inactivos);

        // Mostrar algunos usuarios de ejemplo
        $this->newLine();
        $this->info("👥 Últimos 5 usuarios creados:");
        
        $users = User::latest()->take(5)->get();
        foreach ($users as $user) {
            $this->line("- " . $user->nombre . " " . $user->apellido . " (" . $user->email . ") - " . $user->estado);
        }

        return 0;
    }
} 