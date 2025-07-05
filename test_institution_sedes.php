<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Institucion;

echo "=== PRUEBA DE SEDES DE INSTITUCIÓN ===\n\n";

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
$instituciones = Institucion::all();
echo "📊 Total de instituciones: " . $instituciones->count() . "\n\n";

foreach ($instituciones as $index => $institucion) {
    echo "=== Institución " . ($index + 1) . " ===\n";
    echo "ID: {$institucion->id}\n";
    echo "Nombre: {$institucion->nombre}\n";
    echo "Siglas: {$institucion->siglas}\n";
    echo "Sedes: " . $institucion->sedes()->count() . "\n\n";
}

// 4. Probar la nueva ruta de sedes
echo "4. Probando la nueva ruta de sedes...\n";

// Probar con la primera institución que tenga sedes
$institucionConSedes = Institucion::has('sedes')->first();
if (!$institucionConSedes) {
    echo "❌ No hay instituciones con sedes para probar\n";
    exit(1);
}

echo "🔍 Probando con institución ID: {$institucionConSedes->id} - {$institucionConSedes->nombre}\n\n";

try {
    // Llamar directamente al método del controlador
    $controller = new \App\Http\Controllers\Api\V1\InstitucionController();
    $request = new \Illuminate\Http\Request();
    
    $response = $controller->sedes($request, $institucionConSedes);
    
    echo "✅ Respuesta exitosa:\n";
    
    // Convertir la respuesta a array
    $data = $response->toArray($request);
    
    echo "Total de sedes: " . ($data['total'] ?? 'N/A') . "\n";
    echo "Página actual: " . ($data['current_page'] ?? 'N/A') . "\n";
    echo "Sedes por página: " . ($data['per_page'] ?? 'N/A') . "\n\n";
    
    if (isset($data['data']) && is_array($data['data'])) {
        echo "📋 Sedes encontradas:\n";
        foreach ($data['data'] as $index => $sede) {
            echo "  " . ($index + 1) . ". {$sede['nombre']} - {$sede['direccion']}\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error al probar la ruta: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== PRUEBA COMPLETADA ===\n"; 