<?php

// Script para probar CORS con kampus.test:5173
echo "=== PRUEBA DE CORS CON KAMPUS.TEST:5173 ===\n\n";

// URL base
$baseUrl = 'http://kampus.test';

// 1. Probar CORS en /sanctum/csrf-cookie
echo "1. Probando CORS en /sanctum/csrf-cookie...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/sanctum/csrf-cookie');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Origin: http://kampus.test:5173',
    'Access-Control-Request-Method: GET',
    'Access-Control-Request-Headers: X-Requested-With'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status: $httpCode\n";

// Buscar headers de CORS en la respuesta
if (strpos($response, 'Access-Control-Allow-Origin') !== false) {
    echo "✅ Headers de CORS encontrados\n";
} else {
    echo "❌ Headers de CORS NO encontrados\n";
}

echo "\n";

// 2. Probar CORS en /api/v1/login
echo "2. Probando CORS en /api/v1/login...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/api/v1/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Origin: http://kampus.test:5173',
    'Access-Control-Request-Method: POST',
    'Access-Control-Request-Headers: Content-Type,X-Requested-With'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status: $httpCode\n";

// Buscar headers de CORS en la respuesta
if (strpos($response, 'Access-Control-Allow-Origin') !== false) {
    echo "✅ Headers de CORS encontrados\n";
} else {
    echo "❌ Headers de CORS NO encontrados\n";
}

echo "\n=== FIN DE LA PRUEBA ===\n"; 