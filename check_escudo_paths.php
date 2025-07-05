<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Storage;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== VERIFICANDO RUTAS DE ESCUDOS EN BD ===\n";

try {
    // Usar DB directamente para evitar el error del modelo
    $instituciones = DB::table('instituciones')
        ->whereNotNull('escudo')
        ->get();

    echo "Total de instituciones con escudo: " . $instituciones->count() . "\n\n";

    foreach ($instituciones as $inst) {
        echo "ID: {$inst->id} - Nombre: {$inst->nombre}\n";
        echo "  Escudo (raw): {$inst->escudo}\n";
        
        // Verificar si el archivo existe
        $exists = Storage::disk('public')->exists($inst->escudo);
        echo "  Existe archivo: " . ($exists ? 'SÍ' : 'NO') . "\n";
        
        // Verificar si la ruta es correcta
        $isCorrectPath = str_starts_with($inst->escudo, 'instituciones/escudos/');
        echo "  Ruta correcta: " . ($isCorrectPath ? 'SÍ' : 'NO') . "\n";
        
        if ($exists) {
            $url = Storage::disk('public')->url($inst->escudo);
            echo "  URL: {$url}\n";
        }
        
        echo "---\n";
    }

    // Verificar si hay registros con rutas incorrectas
    $incorrectPaths = DB::table('instituciones')
        ->whereNotNull('escudo')
        ->where('escudo', 'not like', 'instituciones/escudos/%')
        ->get();

    if ($incorrectPaths->count() > 0) {
        echo "\n🚨 REGISTROS CON RUTAS INCORRECTAS:\n";
        foreach ($incorrectPaths as $inst) {
            echo "ID: {$inst->id} - Escudo: {$inst->escudo}\n";
        }
    } else {
        echo "\n✅ Todas las rutas están correctas\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DE LA VERIFICACIÓN ===\n"; 