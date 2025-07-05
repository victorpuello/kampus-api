<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Institucion;

echo "=== DEBUG DEL MODELO INSTITUCIÓN ===\n\n";

// 1. Verificar instituciones directamente desde el modelo
echo "1. Verificando instituciones desde el modelo...\n";
$instituciones = Institucion::all();

echo "📊 Total de instituciones: " . $instituciones->count() . "\n\n";

foreach ($instituciones as $index => $institucion) {
    echo "=== Institución " . ($index + 1) . " ===\n";
    echo "ID: {$institucion->id}\n";
    echo "Nombre: {$institucion->nombre}\n";
    echo "Siglas: {$institucion->siglas}\n";
    echo "Slogan: " . ($institucion->slogan ?? 'NULL') . "\n";
    echo "DANE: " . ($institucion->dane ?? 'NULL') . "\n";
    echo "Dirección: " . ($institucion->direccion ?? 'NULL') . "\n";
    echo "Teléfono: " . ($institucion->telefono ?? 'NULL') . "\n";
    echo "Email: " . ($institucion->email ?? 'NULL') . "\n";
    echo "Rector: " . ($institucion->rector ?? 'NULL') . "\n";
    echo "Escudo: " . ($institucion->escudo ?? 'NULL') . "\n";
    echo "Created at: " . ($institucion->created_at ? $institucion->created_at->toISOString() : 'NULL') . "\n";
    echo "Updated at: " . ($institucion->updated_at ? $institucion->updated_at->toISOString() : 'NULL') . "\n";
    
    // 2. Probar el Resource manualmente
    echo "\n2. Probando Resource manualmente...\n";
    $resource = new \App\Http\Resources\InstitucionResource($institucion);
    $resourceData = $resource->toArray(new \Illuminate\Http\Request());
    
    echo "Resource data:\n";
    foreach ($resourceData as $key => $value) {
        echo "  {$key}: " . (is_null($value) ? 'NULL' : (is_string($value) ? "'{$value}'" : $value)) . "\n";
    }
    
    // 3. Verificar si hay algún problema con las fechas
    echo "\n3. Verificando fechas...\n";
    echo "created_at es Carbon: " . (is_object($institucion->created_at) ? 'Sí' : 'No') . "\n";
    echo "updated_at es Carbon: " . (is_object($institucion->updated_at) ? 'Sí' : 'No') . "\n";
    
    if (is_object($institucion->created_at)) {
        echo "created_at class: " . get_class($institucion->created_at) . "\n";
    }
    if (is_object($institucion->updated_at)) {
        echo "updated_at class: " . get_class($institucion->updated_at) . "\n";
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}

// 4. Probar con una institución específica
echo "4. Probando con institución ID 1 específicamente...\n";
$institucion1 = Institucion::find(1);

if ($institucion1) {
    echo "✅ Institución 1 encontrada\n";
    echo "Datos directos del modelo:\n";
    echo "  ID: {$institucion1->id}\n";
    echo "  Nombre: {$institucion1->nombre}\n";
    echo "  Siglas: {$institucion1->siglas}\n";
    
    echo "\nProbando Resource:\n";
    $resource = new \App\Http\Resources\InstitucionResource($institucion1);
    $resourceData = $resource->toArray(new \Illuminate\Http\Request());
    
    echo "Resource result:\n";
    print_r($resourceData);
} else {
    echo "❌ Institución 1 no encontrada\n";
}

echo "\n=== FIN DEL DEBUG ===\n"; 