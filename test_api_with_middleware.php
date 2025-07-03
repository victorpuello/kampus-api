<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Institucion;

echo "=== PRUEBA DE API CON MIDDLEWARE COMPLETO ===\n\n";

// 1. Verificar que hay usuarios para autenticación
echo "1. Verificando usuarios disponibles...\n";
$user = User::first();
if (!$user) {
    echo "❌ No hay usuarios en la base de datos\n";
    exit(1);
}
echo "✅ Usuario encontrado: {$user->email}\n\n";

// 2. Generar token de autenticación
echo "2. Generando token de autenticación...\n";
$token = $user->createToken('test-token')->plainTextToken;
echo "✅ Token generado: " . substr($token, 0, 20) . "...\n\n";

// 3. Probar la API con el middleware completo
echo "3. Probando API con middleware completo...\n";

$instituciones = Institucion::all(['id', 'nombre', 'siglas']);

foreach ($instituciones as $index => $institucion) {
    echo "=== Institución " . ($index + 1) . " ===\n";
    echo "ID: {$institucion->id}\n";
    echo "Nombre: {$institucion->nombre}\n";
    echo "Siglas: {$institucion->siglas}\n";
    
    // Probar la API
    $url = "http://kampus.test/api/v1/instituciones/{$institucion->id}";
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
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "Status: {$httpCode}\n";
    
    if ($error) {
        echo "❌ Error de cURL: {$error}\n";
    } else {
        $data = json_decode($response, true);
        echo "Response completa: " . $response . "\n";
        
        if ($httpCode === 200) {
            if (isset($data['data'])) {
                $institutionData = $data['data'];
                echo "✅ API devuelve datos en formato correcto\n";
                echo "   - ID: " . ($institutionData['id'] ?? 'NULL') . "\n";
                echo "   - Nombre: " . ($institutionData['nombre'] ?? 'NULL') . "\n";
                echo "   - Siglas: " . ($institutionData['siglas'] ?? 'NULL') . "\n";
                
                // Verificar si hay campos nulos
                $nullFields = [];
                foreach ($institutionData as $key => $value) {
                    if ($value === null) {
                        $nullFields[] = $key;
                    }
                }
                
                if (!empty($nullFields)) {
                    echo "⚠️  Campos nulos encontrados: " . implode(', ', $nullFields) . "\n";
                } else {
                    echo "✅ Todos los campos tienen valores válidos\n";
                }
            } else {
                echo "❌ API no devuelve estructura 'data' esperada\n";
            }
        } else {
            echo "❌ API devuelve error HTTP {$httpCode}\n";
        }
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}

// 4. Probar también la lista de instituciones
echo "4. Probando lista de instituciones...\n";
$url = "http://kampus.test/api/v1/instituciones";
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
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "Status: {$httpCode}\n";

if ($error) {
    echo "❌ Error de cURL: {$error}\n";
} else {
    $data = json_decode($response, true);
    echo "Response de lista: " . substr($response, 0, 1000) . "...\n";
    
    if ($httpCode === 200) {
        if (isset($data['data']) && is_array($data['data'])) {
            echo "✅ Lista devuelve " . count($data['data']) . " instituciones\n";
            
            if (count($data['data']) > 0) {
                $firstInstitution = $data['data'][0];
                echo "Primera institución:\n";
                echo "  - ID: " . ($firstInstitution['id'] ?? 'NULL') . "\n";
                echo "  - Nombre: " . ($firstInstitution['nombre'] ?? 'NULL') . "\n";
                echo "  - Siglas: " . ($firstInstitution['siglas'] ?? 'NULL') . "\n";
            }
        } else {
            echo "❌ Lista no devuelve estructura 'data' esperada\n";
        }
    } else {
        echo "❌ Lista devuelve error HTTP {$httpCode}\n";
    }
}

echo "\n=== FIN DE LA PRUEBA ===\n"; 