<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Debug419Error extends Command
{
    protected $signature = 'debug:419-error';

    protected $description = 'Depurar completamente el error 419 CSRF token mismatch';

    public function handle()
    {
        $this->info('=== DEPURACIÓN COMPLETA DEL ERROR 419 ===');

        // 1. Verificar configuración de sesión
        $this->info('1. CONFIGURACIÓN DE SESIÓN:');
        $this->line('SESSION_DRIVER: '.config('session.driver'));
        $this->line('SESSION_DOMAIN: '.config('session.domain'));
        $this->line('SESSION_SECURE_COOKIE: '.(config('session.secure') ? 'true' : 'false'));
        $this->line('SESSION_SAMESITE: '.config('session.same_site'));
        $this->line('APP_URL: '.config('app.url'));
        $this->line('SANCTUM_STATEFUL_DOMAINS: '.config('sanctum.stateful'));

        // 2. Verificar configuración de CORS
        $this->info('\n2. CONFIGURACIÓN DE CORS:');
        $corsConfig = config('cors');
        $this->line('Paths: '.implode(', ', $corsConfig['paths']));
        $this->line('Allowed origins: '.implode(', ', $corsConfig['allowed_origins']));
        $this->line('Supports credentials: '.($corsConfig['supports_credentials'] ? 'true' : 'false'));

        // 3. Probar obtención de CSRF token
        $this->info('\n3. PRUEBA DE OBTENCIÓN DE CSRF TOKEN:');
        $baseUrl = 'http://kampus.test';
        $cookieFile = 'debug_419_cookies.txt';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl.'/sanctum/csrf-cookie');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->info("Status CSRF: $httpCode");

        // 4. Verificar cookies obtenidas
        $this->info('\n4. VERIFICACIÓN DE COOKIES:');
        if (file_exists($cookieFile)) {
            $cookies = file_get_contents($cookieFile);
            $this->line('Archivo de cookies creado correctamente');

            // Extraer token CSRF
            $xsrfToken = null;
            $lines = explode("\n", $cookies);
            foreach ($lines as $line) {
                if (strpos($line, 'XSRF-TOKEN') !== false && strpos($line, "\t") !== false) {
                    $parts = explode("\t", $line);
                    if (count($parts) >= 7) {
                        $xsrfToken = urldecode(trim($parts[6]));
                        $this->info('✅ Token CSRF extraído: '.substr($xsrfToken, 0, 50).'...');

                        break;
                    }
                }
            }

            if (! $xsrfToken) {
                $this->error('❌ No se pudo extraer el token CSRF');
            }
        } else {
            $this->error('❌ Archivo de cookies no encontrado');
        }

        // 5. Probar login con middleware web (actual)
        $this->info('\n5. PRUEBA DE LOGIN CON MIDDLEWARE WEB (ACTUAL):');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl.'/api/v1/login');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'email' => 'admin@example.com',
            'password' => '123456',
        ]));

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'X-Requested-With: XMLHttpRequest',
        ];

        if (isset($xsrfToken)) {
            $headers[] = 'X-XSRF-TOKEN: '.$xsrfToken;
            $this->info('✅ Header X-XSRF-TOKEN agregado');
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->info("Status Login: $httpCode");
        if ($httpCode !== 200) {
            $this->error("Respuesta: $response");
        } else {
            $this->info('✅ Login exitoso con middleware web');
        }

        // 6. Análisis del problema
        $this->info('\n=== ANÁLISIS DEL PROBLEMA ===');
        $this->line('El problema principal es que la ruta /api/v1/login tiene el middleware "web"');
        $this->line('que incluye verificación CSRF, pero para APIs con Sanctum SPA,');
        $this->line('las rutas de autenticación NO deben tener el middleware "web".');

        // 7. Verificar configuración de sesión en Laravel
        $this->info('\n7. CONFIGURACIÓN DE SESIÓN EN LARAVEL:');
        $this->line('Session driver: '.config('session.driver'));
        $this->line('Session domain: '.config('session.domain'));
        $this->line('Session secure: '.(config('session.secure') ? 'true' : 'false'));
        $this->line('Session same_site: '.config('session.same_site'));

        // 8. Solución
        $this->info('\n=== SOLUCIÓN ===');
        $this->line('1. Remover el middleware "web" de la ruta de login');
        $this->line('2. Mantener solo el middleware "auth:sanctum" para rutas protegidas');
        $this->line('3. Asegurar que la configuración de sesión sea consistente');
        $this->line('4. Corregir SESSION_SAMESITE en .env (actual: none, debe ser: lax)');

        $this->info('\n=== FIN DE DEPURACIÓN ===');

        return 0;
    }
}
