<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckFrontendAuth extends Command
{
    protected $signature = 'frontend:check-auth';
    protected $description = 'Verificar el estado de autenticación del frontend';

    public function handle()
    {
        $this->info('=== VERIFICACIÓN DE AUTENTICACIÓN FRONTEND ===');

        // 1. Verificar configuración de Sanctum
        $this->info('1. CONFIGURACIÓN DE SANCTUM:');
        $this->line('Stateful domains: ' . implode(', ', config('sanctum.stateful')));
        $this->line('Guard: ' . implode(', ', config('sanctum.guard')));
        $this->line('Middleware: ' . implode(', ', array_keys(config('sanctum.middleware'))));

        // 2. Verificar configuración de sesión
        $this->info('\n2. CONFIGURACIÓN DE SESIÓN:');
        $this->line('Driver: ' . config('session.driver'));
        $this->line('Domain: ' . config('session.domain'));
        $this->line('Secure: ' . (config('session.secure') ? 'true' : 'false'));
        $this->line('Same site: ' . config('session.same_site'));
        $this->line('Http only: ' . (config('session.http_only') ? 'true' : 'false'));

        // 3. Verificar configuración de CORS
        $this->info('\n3. CONFIGURACIÓN DE CORS:');
        $corsConfig = config('cors');
        $this->line('Paths: ' . implode(', ', $corsConfig['paths']));
        $this->line('Allowed origins: ' . implode(', ', $corsConfig['allowed_origins']));
        $this->line('Supports credentials: ' . ($corsConfig['supports_credentials'] ? 'true' : 'false'));

        // 4. Verificar rutas
        $this->info('\n4. VERIFICACIÓN DE RUTAS:');
        $routes = app('router')->getRoutes();
        $loginRoute = null;
        $csrfRoute = null;

        foreach ($routes as $route) {
            if ($route->uri() === 'api/v1/login') {
                $loginRoute = $route;
            }
            if ($route->uri() === 'sanctum/csrf-cookie') {
                $csrfRoute = $route;
            }
        }

        if ($loginRoute) {
            $this->line('✅ Ruta /api/v1/login encontrada');
            $this->line('Métodos: ' . implode(', ', $loginRoute->methods()));
            $this->line('Middleware: ' . implode(', ', $loginRoute->middleware()));
        } else {
            $this->error('❌ Ruta /api/v1/login no encontrada');
        }

        if ($csrfRoute) {
            $this->line('✅ Ruta /sanctum/csrf-cookie encontrada');
            $this->line('Métodos: ' . implode(', ', $csrfRoute->methods()));
            $this->line('Middleware: ' . implode(', ', $csrfRoute->middleware()));
        } else {
            $this->error('❌ Ruta /sanctum/csrf-cookie no encontrada');
        }

        // 5. Probar endpoints
        $this->info('\n5. PRUEBA DE ENDPOINTS:');
        
        // Probar CSRF
        $this->line('Probando /sanctum/csrf-cookie...');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://kampus.test/sanctum/csrf-cookie');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'test_csrf_cookies.txt');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $this->line("Status CSRF: $httpCode");
        
        // Probar login
        $this->line('Probando /api/v1/login...');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://kampus.test/api/v1/login');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'email' => 'admin@example.com',
            'password' => '123456'
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'X-Requested-With: XMLHttpRequest'
        ]);
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'test_csrf_cookies.txt');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $this->line("Status Login: $httpCode");
        
        if ($httpCode === 200) {
            $this->info('✅ Login exitoso desde backend');
        } else {
            $this->error("❌ Error en login: $httpCode");
            $this->line("Respuesta: $response");
        }

        // 6. Análisis
        $this->info('\n=== ANÁLISIS ===');
        $this->line('Si el backend funciona pero el frontend no, el problema puede ser:');
        $this->line('1. Cookies no se están enviando correctamente desde el frontend');
        $this->line('2. Origen del frontend no está en SANCTUM_STATEFUL_DOMAINS');
        $this->line('3. Configuración de CORS incorrecta');
        $this->line('4. Headers no se están enviando correctamente');

        $this->info('\n=== RECOMENDACIONES ===');
        $this->line('1. Verificar que el frontend esté en http://kampus.test:5173');
        $this->line('2. Verificar que las cookies se estén enviando');
        $this->line('3. Verificar que el header X-XSRF-TOKEN se esté enviando');
        $this->line('4. Usar la página de debug para verificar el comportamiento');

        return 0;
    }
} 