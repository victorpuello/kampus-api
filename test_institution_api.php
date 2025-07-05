<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

// Obtener un token de prueba
$user = User::first();
if (!$user) {
    echo "No hay usuarios en la base de datos\n";
    exit(1);
}

$token = $user->createToken('test-token')->plainTextToken;
echo "Token generado: " . substr($token, 0, 20) . "...\n\n";

// Probar la API
$url = 'http://kampus.test/api/v1/instituciones/4';
$headers = [
    'Accept: application/json',
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "URL: $url\n";
echo "HTTP Code: $httpCode\n";
echo "Response:\n";
echo $response . "\n";

// Probar también con ID 1
echo "\n--- Probando con ID 1 ---\n";
$url = 'http://kampus.test/api/v1/instituciones/1';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "URL: $url\n";
echo "HTTP Code: $httpCode\n";
echo "Response:\n";
echo $response . "\n"; 