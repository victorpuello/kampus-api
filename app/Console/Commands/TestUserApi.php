<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Console\Command;

class TestUserApi extends Command
{
    protected $signature = 'users:test-api';
    protected $description = 'Prueba la API de usuarios';

    public function handle()
    {
        $this->info('🧪 Probando API de usuarios...');
        
        try {
            $controller = new UserController();
            $response = $controller->index();
            
            $this->info('✅ API funcionando correctamente');
            $this->info('📊 Cantidad de usuarios devueltos: ' . count($response->collection));
            
            // Mostrar algunos usuarios de ejemplo
            $this->newLine();
            $this->info('👥 Primeros 3 usuarios:');
            
            $users = $response->collection->take(3);
            foreach ($users as $user) {
                $this->line("- " . $user->nombre . " " . $user->apellido . " (" . $user->email . ")");
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error en la API: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
} 