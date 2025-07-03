<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Institucion;

echo "=== DEBUG DE API DE INSTITUCIONES ===\n\n";

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

// 3. Verificar instituciones disponibles
echo "3. Verificando instituciones disponibles...\n";
$instituciones = Institucion::all(['id', 'nombre', 'siglas']);
echo "📊 Total de instituciones: " . $instituciones->count() . "\n\n";

foreach ($instituciones as $index => $institucion) {
    echo "=== Institución " . ($index + 1) . " ===\n";
    echo "ID: {$institucion->id}\n";
    echo "Nombre: {$institucion->nombre}\n";
    echo "Siglas: {$institucion->siglas}\n";
    
    // 4. Probar la API para cada institución
    echo "4. Probando API para institución ID {$institucion->id}...\n";
    
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
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "Status: {$httpCode}\n";
    
    if ($error) {
        echo "❌ Error de cURL: {$error}\n";
    } else {
        $data = json_decode($response, true);
        echo "Response: " . substr($response, 0, 500) . "...\n";
        
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

echo "=== FIN DEL DEBUG ===\n"; 