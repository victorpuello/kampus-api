<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Console\Command;

class TestUserResponse extends Command
{
    protected $signature = 'users:test-response';
    protected $description = 'Simula la respuesta de la API de usuarios';

    public function handle()
    {
        $this->info('🧪 Simulando respuesta de la API de usuarios...');
        
        try {
            $controller = new UserController();
            $response = $controller->index();
            
            // Simular la respuesta como la recibiría el frontend
            $responseData = $response->response()->getData(true);
            
            $this->info('✅ Respuesta simulada correctamente');
            $this->info('📊 Estructura de la respuesta:');
            $this->info('- response.data: ' . (isset($responseData['data']) ? count($responseData['data']) : 'No existe'));
            $this->info('- response (directo): ' . count($responseData));
            
            // Mostrar algunos usuarios de ejemplo
            $this->newLine();
            $this->info('👥 Primeros 3 usuarios de la respuesta:');
            
            $users = isset($responseData['data']) ? $responseData['data'] : $responseData;
            $users = array_slice($users, 0, 3);
            
            foreach ($users as $user) {
                $this->line("- " . $user['nombre'] . " " . $user['apellido'] . " (" . $user['email'] . ")");
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error al simular la respuesta: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
} 