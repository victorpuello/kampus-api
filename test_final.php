<?php

echo "=== PRUEBA FINAL DEL ENDPOINT DE LOGIN ===\n";

$url = 'http://kampus.test/api/v1/login';
$data = [
    'email' => 'admin@example.com',
    'password' => '123456'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Error: " . ($error ?: 'Ninguno') . "\n";
echo "Response:\n$response\n";

if ($httpCode === 200) {
    echo "✅ ¡LOGIN EXITOSO!\n";
} else {
    echo "❌ Login falló\n";
}

echo "=== FIN DE PRUEBA ===\n"; 