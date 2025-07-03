<?php

require_once 'vendor/autoload.php';

use App\Models\Institucion;

// Simular entorno Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== LIMPIANDO REGISTROS CON IMÁGENES INEXISTENTES ===\n\n";

try {
    // 1. Obtener todas las instituciones con escudo
    $instituciones = Institucion::whereNotNull('escudo')->get();
    
    echo "📊 Total de instituciones con escudo: " . $instituciones->count() . "\n\n";
    
    $cleaned = 0;
    $valid = 0;
    
    foreach ($instituciones as $institucion) {
        echo "🔍 Verificando: " . $institucion->nombre . "\n";
        echo "   Escudo: " . $institucion->escudo . "\n";
        
        // Verificar si el archivo existe
        $filePath = storage_path('app/public/' . $institucion->escudo);
        $fileExists = file_exists($filePath);
        
        echo "   Ruta completa: " . $filePath . "\n";
        echo "   Existe: " . ($fileExists ? '✅' : '❌') . "\n";
        
        if (!$fileExists) {
            echo "   🧹 Limpiando campo escudo...\n";
            $institucion->escudo = null;
            $institucion->save();
            $cleaned++;
            echo "   ✅ Limpiado\n";
        } else {
            $valid++;
            echo "   ✅ Válido\n";
        }
        
        echo "\n";
    }
    
    echo "=== RESUMEN ===\n";
    echo "📊 Total revisado: " . $instituciones->count() . "\n";
    echo "✅ Válidos: " . $valid . "\n";
    echo "🧹 Limpiados: " . $cleaned . "\n";
    
    if ($cleaned > 0) {
        echo "\n🎉 Se limpiaron " . $cleaned . " registros con imágenes inexistentes.\n";
    } else {
        echo "\n✨ Todos los registros están correctos.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
}

echo "\n=== FIN DE LA LIMPIEZA ===\n"; 