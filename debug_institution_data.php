<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Institucion;

echo "=== DEBUG DE DATOS DE INSTITUCIONES ===\n\n";

// Verificar todas las instituciones
$instituciones = Institucion::all();

echo "📊 Total de instituciones: " . $instituciones->count() . "\n\n";

foreach ($instituciones as $index => $institucion) {
    echo "=== Institución " . ($index + 1) . " ===\n";
    echo "ID: " . $institucion->id . "\n";
    echo "Nombre: '" . $institucion->nombre . "'\n";
    echo "Siglas: '" . $institucion->siglas . "'\n";
    echo "Slogan: '" . ($institucion->slogan ?? 'NULL') . "'\n";
    echo "DANE: '" . ($institucion->dane ?? 'NULL') . "'\n";
    echo "Resolución: '" . ($institucion->resolucion_aprobacion ?? 'NULL') . "'\n";
    echo "Dirección: '" . ($institucion->direccion ?? 'NULL') . "'\n";
    echo "Teléfono: '" . ($institucion->telefono ?? 'NULL') . "'\n";
    echo "Email: '" . ($institucion->email ?? 'NULL') . "'\n";
    echo "Rector: '" . ($institucion->rector ?? 'NULL') . "'\n";
    echo "Escudo: '" . ($institucion->escudo ?? 'NULL') . "'\n";
    echo "Created: " . $institucion->created_at . "\n";
    echo "Updated: " . $institucion->updated_at . "\n";
    
    // Verificar sedes
    $sedes = $institucion->sedes;
    echo "Sedes: " . $sedes->count() . "\n";
    foreach ($sedes as $sede) {
        echo "  - Sede ID: {$sede->id}, Nombre: {$sede->nombre}\n";
    }
    echo "\n";
}

// Verificar específicamente la institución con ID 1
echo "=== VERIFICACIÓN ESPECÍFICA ID 1 ===\n";
$institucion1 = Institucion::find(1);
if ($institucion1) {
    echo "✅ Institución 1 encontrada\n";
    echo "Nombre: '{$institucion1->nombre}'\n";
    echo "Siglas: '{$institucion1->siglas}'\n";
    
    // Probar el Resource manualmente
    echo "\n=== PRUEBA DEL RESOURCE ===\n";
    $resource = new \App\Http\Resources\InstitucionResource($institucion1);
    $array = $resource->toArray(new \Illuminate\Http\Request());
    echo "Resource array:\n";
    print_r($array);
} else {
    echo "❌ Institución 1 no encontrada\n";
}

echo "\n=== FIN DEL DEBUG ===\n"; 