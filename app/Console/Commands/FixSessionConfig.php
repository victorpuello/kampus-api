<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixSessionConfig extends Command
{
    protected $signature = 'session:fix-config';

    protected $description = 'Cambiar temporalmente la configuración de sesión para desarrollo';

    public function handle()
    {
        $this->info('=== CAMBIANDO CONFIGURACIÓN DE SESIÓN ===');

        // Cambiar la configuración de sesión temporalmente
        config(['session.secure' => false]);
        config(['session.same_site' => 'lax']);

        $this->info('✅ Configuración cambiada:');
        $this->info('- SESSION_SECURE_COOKIE = false');
        $this->info('- SESSION_SAMESITE = lax');

        $this->info('\n=== PROBANDO CONFIGURACIÓN ===');

        // URL base
        $baseUrl = 'http://kampus.test';

        // Archivo para cookies
        $cookieFile = 'test_cookies.txt';

        // 1. Obtener CSRF token
        $this->info('1. Obteniendo CSRF token...');
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

        // Mostrar cookies recibidas
        if (file_exists($cookieFile)) {
            $this->info('\nCookies recibidas:');
            $cookies = file_get_contents($cookieFile);
            $this->line($cookies);

            // Buscar XSRF-TOKEN
            if (preg_match('/XSRF-TOKEN\\s+([^\\s]+)/', $cookies, $matches)) {
                $xsrfToken = urldecode($matches[1]);
                $this->info('\n✅ Token CSRF encontrado: '.substr($xsrfToken, 0, 50).'...');
            } else {
                $this->error('\n❌ Token CSRF NO encontrado');
            }
        } else {
            $this->error('❌ Archivo de cookies no encontrado');
        }

        $this->info('');

        // 2. Intentar login
        $this->info('2. Intentando login...');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl.'/api/v1/login');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'email' => 'admin@example.com',
            'password' => '123456',
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'X-Requested-With: XMLHttpRequest',
        ]);

        // Agregar cookies
        if (file_exists($cookieFile)) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
            $this->info('✅ Archivo de cookies agregado');
        }

        // Agregar header X-XSRF-TOKEN
        if (isset($xsrfToken)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json',
                'X-Requested-With: XMLHttpRequest',
                'X-XSRF-TOKEN: '.$xsrfToken,
            ]);
            $this->info('✅ Header X-XSRF-TOKEN agregado');
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

        $this->info('\n=== FIN DE PRUEBA ===');

        return 0;
    }
}
