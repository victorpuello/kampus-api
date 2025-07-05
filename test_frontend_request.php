<?php

require_once 'vendor/autoload.php';

use App\Models\Institucion;

// Simular entorno Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== SIMULANDO PETICIÓN DEL FRONTEND ===\n\n";

try {
    // 1. Buscar una institución con escudo
    echo "1️⃣ Buscando institución con escudo...\n";
    
    $institucion = Institucion::whereNotNull('escudo')->first();
    
    if (!$institucion) {
        echo "❌ No hay instituciones con escudo en la base de datos\n";
        exit;
    }
    
    echo "✅ Institución encontrada: " . $institucion->nombre . "\n";
    echo "   ID: " . $institucion->id . "\n";
    echo "   Escudo (raw): " . $institucion->escudo . "\n";
    
    // 2. Simular el Resource
    echo "\n2️⃣ Simulando InstitucionResource...\n";
    
    $resource = new \App\Http\Resources\InstitucionResource($institucion);
    $data = $resource->toArray(new \Illuminate\Http\Request());
    
    echo "✅ Datos del Resource:\n";
    echo "   - ID: " . $data['id'] . "\n";
    echo "   - Nombre: " . $data['nombre'] . "\n";
    echo "   - Escudo: " . $data['escudo'] . "\n";
    
    // 3. Verificar URL del archivo
    echo "\n3️⃣ Verificando URL del archivo...\n";
    
    $escudoUrl = $institucion->getFileUrl('escudo');
    echo "   getFileUrl(): " . $escudoUrl . "\n";
    
    $assetUrl = asset('storage/' . $institucion->escudo);
    echo "   asset(): " . $assetUrl . "\n";
    
    // 4. Verificar si el archivo existe
    echo "\n4️⃣ Verificando existencia del archivo...\n";
    
    $filePath = storage_path('app/public/' . $institucion->escudo);
    echo "   Ruta del archivo: " . $filePath . "\n";
    echo "   Archivo existe: " . (file_exists($filePath) ? '✅' : '❌') . "\n";
    
    if (file_exists($filePath)) {
        echo "   Tamaño: " . filesize($filePath) . " bytes\n";
        echo "   MIME: " . mime_content_type($filePath) . "\n";
    }
    
    // 5. Simular petición HTTP
    echo "\n5️⃣ Simulando petición HTTP...\n";
    
    $request = \Illuminate\Http\Request::create("/api/v1/instituciones/{$institucion->id}", 'GET');
    $request->headers->set('Accept', 'application/json');
    $request->headers->set('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    
    $response = app()->handle($request);
    
    echo "   Status: " . $response->getStatusCode() . "\n";
    echo "   Content-Type: " . $response->headers->get('Content-Type') . "\n";
    
    $responseData = json_decode($response->getContent(), true);
    echo "   Escudo en respuesta: " . ($responseData['data']['escudo'] ?? 'null') . "\n";
    
    // 6. Probar acceso directo al archivo
    echo "\n6️⃣ Probando acceso directo al archivo...\n";
    
    if ($escudoUrl) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $escudoUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "   URL probada: " . $escudoUrl . "\n";
        echo "   Status HTTP: " . $httpCode . "\n";
        echo "   Accesible: " . ($httpCode === 200 ? '✅' : '❌') . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
}

echo "\n=== FIN DE LA SIMULACIÓN ===\n"; 