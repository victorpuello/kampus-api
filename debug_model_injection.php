<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Institucion;
use App\Http\Controllers\Api\V1\InstitucionController;
use Illuminate\Http\Request;

echo "=== DEBUG DE INYECCIÓN DE MODELO ===\n\n";

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
    
    // 4. Probar la inyección de modelo manualmente
    echo "4. Probando inyección de modelo manualmente...\n";
    
    // Crear una instancia del controlador
    $controller = new InstitucionController();
    
    // Crear una request simulada
    $request = new Request();
    $request->merge(['include' => 'sedes']);
    
    // Probar la inyección de modelo
    try {
        // Simular la inyección de modelo
        $injectedInstitution = Institucion::find($institucion->id);
        
        if ($injectedInstitution) {
            echo "✅ Institución encontrada por inyección: {$injectedInstitution->id}\n";
            echo "   - Nombre: {$injectedInstitution->nombre}\n";
            echo "   - Siglas: {$injectedInstitution->siglas}\n";
            
            // Probar el Resource
            $resource = new \App\Http\Resources\InstitucionResource($injectedInstitution);
            $resourceData = $resource->toArray($request);
            
            echo "   - Resource ID: " . ($resourceData['id'] ?? 'NULL') . "\n";
            echo "   - Resource Nombre: " . ($resourceData['nombre'] ?? 'NULL') . "\n";
            echo "   - Resource Siglas: " . ($resourceData['siglas'] ?? 'NULL') . "\n";
            
            // Verificar si hay campos nulos
            $nullFields = [];
            foreach ($resourceData as $key => $value) {
                if ($value === null) {
                    $nullFields[] = $key;
                }
            }
            
            if (!empty($nullFields)) {
                echo "⚠️  Campos nulos en Resource: " . implode(', ', $nullFields) . "\n";
            } else {
                echo "✅ Resource devuelve todos los campos correctamente\n";
            }
        } else {
            echo "❌ Institución no encontrada por inyección\n";
        }
    } catch (Exception $e) {
        echo "❌ Error en inyección de modelo: " . $e->getMessage() . "\n";
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}

// 5. Probar con una institución específica usando el controlador
echo "5. Probando con controlador específicamente...\n";
$controller = new InstitucionController();
$request = new Request();
$request->merge(['include' => 'sedes']);

$institucion1 = Institucion::find(1);
if ($institucion1) {
    echo "✅ Institución 1 encontrada directamente\n";
    
    try {
        // Simular el método show del controlador
        $result = $controller->show($request, $institucion1);
        
        echo "✅ Método show ejecutado correctamente\n";
        echo "Tipo de resultado: " . get_class($result) . "\n";
        
        // Convertir a array para ver el contenido
        $resultArray = $result->toArray($request);
        echo "Resultado del método show:\n";
        print_r($resultArray);
        
    } catch (Exception $e) {
        echo "❌ Error en método show: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Institución 1 no encontrada\n";
}

echo "\n=== FIN DEL DEBUG ===\n"; 