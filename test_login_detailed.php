<?php

// Script para probar el login con Sanctum - Versión detallada
echo "=== PRUEBA DETALLADA DE LOGIN CON SANCTUM ===\n\n";

// URL base
$baseUrl = 'http://kampus.test';

// Archivo para cookies
$cookieFile = 'cookies_detailed.txt';

// 1. Obtener CSRF token
echo "1. Obteniendo CSRF token...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/sanctum/csrf-cookie');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_VERBOSE, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status CSRF: $httpCode\n";

if ($httpCode !== 200 && $httpCode !== 204) {
    echo "❌ Error al obtener CSRF token\n";
    exit(1);
}

echo "✅ CSRF token obtenido correctamente\n\n";

// 2. Verificar cookies
echo "2. Verificando cookies...\n";
if (file_exists($cookieFile)) {
    $cookies = file_get_contents($cookieFile);
    echo "Cookies guardadas:\n";
    echo $cookies . "\n";
    
    // Buscar XSRF-TOKEN
    if (strpos($cookies, 'XSRF-TOKEN') !== false) {
        echo "✅ Cookie XSRF-TOKEN encontrada\n";
    } else {
        echo "❌ Cookie XSRF-TOKEN NO encontrada\n";
    }
    
    // Buscar laravel_session
    if (strpos($cookies, 'laravel_session') !== false) {
        echo "✅ Cookie laravel_session encontrada\n";
    } else {
        echo "❌ Cookie laravel_session NO encontrada\n";
    }
} else {
    echo "❌ Archivo de cookies no encontrado\n";
}

echo "\n";

// 3. Leer el token CSRF de las cookies
echo "3. Leyendo token CSRF de cookies...\n";
$xsrfToken = null;
if (file_exists($cookieFile)) {
    $cookies = file_get_contents($cookieFile);
    if (preg_match('/XSRF-TOKEN\s+([^\s]+)/', $cookies, $matches)) {
        $xsrfToken = urldecode($matches[1]);
        echo "✅ Token CSRF encontrado: " . substr($xsrfToken, 0, 50) . "...\n";
    } else {
        echo "❌ Token CSRF no encontrado en cookies\n";
    }
}

// 4. Intentar login
echo "4. Intentando login...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/api/v1/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => 'admin@example.com',
    'password' => '123456'
]));

$headers = [
    'Content-Type: application/json',
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest'
];

if ($xsrfToken) {
    $headers[] = 'X-XSRF-TOKEN: ' . $xsrfToken;
    echo "✅ Header X-XSRF-TOKEN agregado\n";
} else {
    echo "❌ No se pudo agregar header X-XSRF-TOKEN\n";
}

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_VERBOSE, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status Login: $httpCode\n";
echo "Response: $response\n\n";

if ($httpCode === 200) {
    echo "✅ Login exitoso!\n";
    $data = json_decode($response, true);
    if (isset($data['user'])) {
        echo "Usuario: " . $data['user']['nombre'] . " " . $data['user']['apellido'] . "\n";
        echo "Email: " . $data['user']['email'] . "\n";
    }
} else {
    echo "❌ Error en login\n";
}

// Limpiar archivo de cookies
if (file_exists($cookieFile)) {
    unlink($cookieFile);
}

echo "\n=== FIN DE PRUEBA ===\n"; 