<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== PRUEBA DEL MÉTODO GETFILEURL ===\n";

try {
    // Buscar una institución con escudo
    $institucion = App\Models\Institucion::whereNotNull('escudo')->first();
    
    if (!$institucion) {
        echo "❌ No se encontró ninguna institución con escudo\n";
        exit;
    }
    
    echo "✅ Institución encontrada: {$institucion->nombre}\n";
    echo "📝 Campo escudo (raw): {$institucion->escudo}\n";
    
    // Verificar configuración de campos
    echo "\n🔧 Verificando configuración de campos:\n";
    echo "   - fileFields: " . json_encode($institucion->fileFields ?? []) . "\n";
    echo "   - filePaths: " . json_encode($institucion->filePaths ?? []) . "\n";
    
    // Probar getFileUrl
    echo "\n🔗 Probando getFileUrl:\n";
    $fileUrl = $institucion->getFileUrl('escudo');
    echo "   - getFileUrl('escudo'): " . ($fileUrl ?? 'null') . "\n";
    
    // Probar asset
    $assetUrl = asset('storage/' . $institucion->escudo);
    echo "   - asset('storage/' . escudo): {$assetUrl}\n";
    
    // Verificar si el archivo existe
    if ($institucion->escudo) {
        $exists = Storage::disk('public')->exists($institucion->escudo);
        echo "   - Archivo existe en storage: " . ($exists ? 'SÍ' : 'NO') . "\n";
    }
    
    // Probar InstitucionResource
    echo "\n📦 Probando InstitucionResource:\n";
    $resource = new App\Http\Resources\InstitucionResource($institucion);
    $request = new Illuminate\Http\Request();
    $data = $resource->toArray($request);
    
    echo "   - escudo en resource: " . ($data['escudo'] ?? 'null') . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 Línea: " . $e->getLine() . "\n";
    echo "📁 Archivo: " . $e->getFile() . "\n";
}

echo "\n=== FIN DE LA PRUEBA ===\n"; 