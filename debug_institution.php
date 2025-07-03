<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Institucion;

echo "Debug de institución ID 4:\n";
echo "========================\n";

$institucion = Institucion::find(4);

if (!$institucion) {
    echo "❌ Institución con ID 4 no encontrada\n";
    exit(1);
}

echo "✅ Institución encontrada:\n";
echo "ID: " . $institucion->id . "\n";
echo "Nombre: " . $institucion->nombre . "\n";
echo "Siglas: " . $institucion->siglas . "\n";
echo "Slogan: " . ($institucion->slogan ?? 'NULL') . "\n";
echo "DANE: " . ($institucion->dane ?? 'NULL') . "\n";
echo "Dirección: " . ($institucion->direccion ?? 'NULL') . "\n";
echo "Teléfono: " . ($institucion->telefono ?? 'NULL') . "\n";
echo "Email: " . ($institucion->email ?? 'NULL') . "\n";
echo "Rector: " . ($institucion->rector ?? 'NULL') . "\n";
echo "Escudo: " . ($institucion->escudo ?? 'NULL') . "\n";
echo "Created: " . $institucion->created_at . "\n";
echo "Updated: " . $institucion->updated_at . "\n";

echo "\n--- Datos raw de la base de datos ---\n";
$raw = Institucion::select('*')->where('id', 4)->first();
if ($raw) {
    foreach ($raw->getAttributes() as $key => $value) {
        echo "$key: " . (is_null($value) ? 'NULL' : $value) . "\n";
    }
}

echo "\n--- Probando Resource ---\n";
$resource = new \App\Http\Resources\InstitucionResource($institucion);
$array = $resource->toArray(new \Illuminate\Http\Request());
echo json_encode($array, JSON_PRETTY_PRINT) . "\n"; 