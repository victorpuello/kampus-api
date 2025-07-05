<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class DevAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Solo en desarrollo
        $env = $_ENV['APP_ENV'] ?? 'production';
        if (in_array($env, ['local', 'development'])) {
            // Buscar usuario admin o crear uno si no existe
            $user = User::where('email', 'admin@example.com')->first();
            
            if (!$user) {
                // Crear usuario admin si no existe
                $user = User::create([
                    'name' => 'Admin',
                    'email' => 'admin@example.com',
                    'password' => bcrypt('123456'),
                ]);
            }
            
            // Autenticar automáticamente con Sanctum
            Auth::guard('sanctum')->setUser($user);
            
            // Agregar header para identificar que es autenticación de desarrollo
            $response = $next($request);
            $response->headers->set('X-Dev-Auth', 'true');
            
            return $response;
        }
        
        return $next($request);
    }
}
