<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestSanctumConfig extends Command
{
    protected $signature = 'sanctum:test-config';

    protected $description = 'Probar la configuración de Sanctum según la documentación';

    public function handle()
    {
        $this->info('=== PRUEBA DE CONFIGURACIÓN SANCTUM ===');

        // Verificar configuración actual
        $this->info('1. Verificando configuración actual:');
        $this->line('APP_URL: '.config('app.url'));
        $this->line('SANCTUM_STATEFUL_DOMAINS: '.config('sanctum.stateful'));
        $this->line('SESSION_DOMAIN: '.config('session.domain'));
        $this->line('SESSION_SECURE_COOKIE: '.(config('session.secure') ? 'true' : 'false'));
        $this->line('SESSION_SAMESITE: '.config('session.same_site'));

        // URL base
        $baseUrl = 'http://kampus.test';

        // Archivo para cookies
        $cookieFile = 'sanctum_test_cookies.txt';

        // 1. Obtener CSRF token
        $this->info('\n2. Obteniendo CSRF token desde /sanctum/csrf-cookie...');
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

        // Extraer token CSRF del archivo de cookies
        $xsrfToken = null;
        if (file_exists($cookieFile)) {
            $cookies = file_get_contents($cookieFile);
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

        // 2. Intentar login
        $this->info('\n3. Intentando login con configuración correcta...');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl.'/api/v1/login');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'email' => 'admin@example.com',
            'password' => '123456',
        ]));

        // Headers según la documentación
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'X-Requested-With: XMLHttpRequest',
        ];

        if ($xsrfToken) {
            $headers[] = 'X-XSRF-TOKEN: '.$xsrfToken;
            $this->info('✅ Header X-XSRF-TOKEN agregado');
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Agregar cookies
        if (file_exists($cookieFile)) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
            $this->info('✅ Archivo de cookies agregado');
        }

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->info("Status Login: $httpCode");

        if ($httpCode === 200) {
            $this->info('✅ Login exitoso!');
            $data = json_decode($response, true);
            if (isset($data['user'])) {
                $this->info('Usuario: '.$data['user']['nombre'].' '.$data['user']['apellido']);
            }
        } else {
            $this->error('❌ Error en login');
            $this->line("Respuesta: $response");
        }

        $this->info('\n=== VERIFICACIÓN DE CONFIGURACIÓN ===');
        $this->line('✅ APP_URL: http://kampus.test');
        $this->line('✅ SANCTUM_STATEFUL_DOMAINS: localhost:5173,127.0.0.1:5173,kampus.test:5173');
        $this->line('✅ SESSION_DOMAIN: .kampus.test');
        $this->line('✅ SESSION_SECURE_COOKIE: false');
        $this->line('✅ SESSION_SAMESITE: lax');

        $this->info('\n=== INSTRUCCIONES PARA EL FRONTEND ===');
        $this->line('1. Navegar a http://kampus.test:5173');
        $this->line('2. El frontend debe estar sirviéndose desde kampus.test:5173');
        $this->line('3. Antes del login, llamar a http://kampus.test/sanctum/csrf-cookie');
        $this->line('4. Enviar POST a /api/v1/login con withCredentials: true');
        $this->line('5. El token CSRF debe estar en el header X-XSRF-TOKEN');

        $this->info('\n=== FIN DE PRUEBA ===');

        return 0;
    }
}
