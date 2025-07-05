<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Storage;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CORRIGIENDO RUTAS DE ESCUDOS EN BD ===\n";

try {
    // Obtener instituciones con rutas incorrectas
    $incorrectPaths = DB::table('instituciones')
        ->whereNotNull('escudo')
        ->where(function($query) {
            $query->where('escudo', 'not like', 'instituciones/escudos/%')
                  ->orWhere('escudo', 'like', 'C:%')  // Rutas temporales de Windows
                  ->orWhere('escudo', 'like', '/tmp/%'); // Rutas temporales de Linux
        })
        ->get();

    echo "Total de registros con rutas incorrectas: " . $incorrectPaths->count() . "\n\n";

    $fixed = 0;
    $deleted = 0;

    foreach ($incorrectPaths as $inst) {
        echo "Procesando ID: {$inst->id} - Nombre: {$inst->nombre}\n";
        echo "  Escudo actual: {$inst->escudo}\n";
        
        // Verificar si es una ruta temporal
        if (str_contains($inst->escudo, 'C:\\') || str_contains($inst->escudo, '/tmp/') || str_contains($inst->escudo, 'php')) {
            echo "  → Es una ruta temporal, eliminando...\n";
            DB::table('instituciones')
                ->where('id', $inst->id)
                ->update(['escudo' => null]);
            $deleted++;
        } 
        // Verificar si es una ruta de escudos sin prefijo
        elseif (str_starts_with($inst->escudo, 'escudos/')) {
            $newPath = 'instituciones/' . $inst->escudo;
            echo "  → Corrigiendo ruta: {$newPath}\n";
            
            // Verificar si el archivo existe en la nueva ubicación
            if (Storage::disk('public')->exists($newPath)) {
                echo "  → Archivo existe en nueva ubicación\n";
                DB::table('instituciones')
                    ->where('id', $inst->id)
                    ->update(['escudo' => $newPath]);
                $fixed++;
            } else {
                echo "  → Archivo no existe, eliminando referencia...\n";
                DB::table('instituciones')
                    ->where('id', $inst->id)
                    ->update(['escudo' => null]);
                $deleted++;
            }
        } else {
            echo "  → Ruta desconocida, eliminando...\n";
            DB::table('instituciones')
                ->where('id', $inst->id)
                ->update(['escudo' => null]);
            $deleted++;
        }
        
        echo "---\n";
    }

    echo "\n📊 RESUMEN:\n";
    echo "  - Registros corregidos: {$fixed}\n";
    echo "  - Referencias eliminadas: {$deleted}\n";
    echo "  - Total procesados: " . ($fixed + $deleted) . "\n";

    // Verificar resultado final
    $remainingIncorrect = DB::table('instituciones')
        ->whereNotNull('escudo')
        ->where('escudo', 'not like', 'instituciones/escudos/%')
        ->count();

    if ($remainingIncorrect == 0) {
        echo "\n✅ Todas las rutas han sido corregidas\n";
    } else {
        echo "\n⚠️  Aún quedan {$remainingIncorrect} registros con rutas incorrectas\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DE LA CORRECCIÓN ===\n"; 