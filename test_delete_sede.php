<?php

require_once 'vendor/autoload.php';

use App\Models\Sede;
use App\Models\Institucion;
use App\Models\User;

// Configurar la aplicación Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PRUEBA DE ELIMINACIÓN DE SEDES ===\n\n";

// 1. Verificar que existan sedes para eliminar
echo "1. Verificando sedes existentes...\n";
$sedes = Sede::with('institucion')->get();

if ($sedes->isEmpty()) {
    echo "❌ No hay sedes en la base de datos para probar la eliminación\n";
    exit;
}

echo "✅ Se encontraron " . $sedes->count() . " sedes:\n";
foreach ($sedes as $sede) {
    echo "   - ID: {$sede->id}, Nombre: {$sede->nombre}, Institución: {$sede->institucion->nombre}\n";
}

// 2. Seleccionar una sede para eliminar
$sedeParaEliminar = $sedes->first();
echo "\n2. Seleccionando sede para eliminar: {$sedeParaEliminar->nombre} (ID: {$sedeParaEliminar->id})\n";

// 3. Verificar que el usuario admin existe
echo "\n3. Verificando usuario administrador...\n";
$adminUser = User::where('email', 'admin@example.com')->first();

if (!$adminUser) {
    echo "❌ No se encontró el usuario administrador\n";
    exit;
}

echo "✅ Usuario administrador encontrado: {$adminUser->nombre} {$adminUser->apellido}\n";

// 4. Crear token de autenticación
echo "\n4. Creando token de autenticación...\n";
$token = $adminUser->createToken('test-token')->plainTextToken;
echo "✅ Token creado: " . substr($token, 0, 20) . "...\n";

// 5. Probar la eliminación usando el controlador
echo "\n5. Probando eliminación usando el controlador...\n";

try {
    $controller = new \App\Http\Controllers\Api\V1\SedeController();
    
    // Simular la inyección de modelo
    $sede = Sede::find($sedeParaEliminar->id);
    
    if (!$sede) {
        echo "❌ La sede no fue encontrada\n";
        exit;
    }
    
    echo "✅ Sede encontrada para eliminar: {$sede->nombre}\n";
    
    // Llamar al método destroy
    $response = $controller->destroy($sede);
    
    echo "✅ Respuesta del controlador:\n";
    echo "   Status: " . $response->getStatusCode() . "\n";
    echo "   Content: " . $response->getContent() . "\n";
    
    // Verificar que la sede fue eliminada (soft delete)
    $sedeEliminada = Sede::withTrashed()->find($sedeParaEliminar->id);
    
    if ($sedeEliminada && $sedeEliminada->trashed()) {
        echo "✅ La sede fue eliminada correctamente (soft delete)\n";
        echo "   Fecha de eliminación: " . $sedeEliminada->deleted_at . "\n";
    } else {
        echo "❌ La sede no fue eliminada correctamente\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error al eliminar la sede: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

// 6. Probar la API REST directamente
echo "\n6. Probando API REST directamente...\n";

try {
    $client = new \GuzzleHttp\Client([
        'base_uri' => 'http://kampus.test',
        'headers' => [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]
    ]);
    
    // Buscar otra sede para eliminar
    $otraSede = Sede::withTrashed()->where('id', '!=', $sedeParaEliminar->id)->first();
    
    if (!$otraSede) {
        echo "❌ No hay otra sede disponible para probar la API\n";
    } else {
        echo "✅ Probando eliminación de sede ID: {$otraSede->id} via API\n";
        
        $response = $client->delete("/api/v1/sedes/{$otraSede->id}");
        
        echo "✅ Respuesta de la API:\n";
        echo "   Status: " . $response->getStatusCode() . "\n";
        echo "   Content: " . $response->getBody()->getContents() . "\n";
        
        // Verificar que la sede fue eliminada
        $sedeEliminadaAPI = Sede::withTrashed()->find($otraSede->id);
        
        if ($sedeEliminadaAPI && $sedeEliminadaAPI->trashed()) {
            echo "✅ La sede fue eliminada correctamente via API\n";
        } else {
            echo "❌ La sede no fue eliminada correctamente via API\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error al probar la API: " . $e->getMessage() . "\n";
}

// 7. Verificar el estado final
echo "\n7. Estado final de las sedes...\n";
$sedesRestantes = Sede::with('institucion')->get();
$sedesEliminadas = Sede::onlyTrashed()->get();

echo "✅ Sedes activas: " . $sedesRestantes->count() . "\n";
echo "✅ Sedes eliminadas (soft delete): " . $sedesEliminadas->count() . "\n";

if ($sedesEliminadas->count() > 0) {
    echo "   Sedes eliminadas:\n";
    foreach ($sedesEliminadas as $sede) {
        echo "   - ID: {$sede->id}, Nombre: {$sede->nombre}, Eliminada: {$sede->deleted_at}\n";
    }
}

echo "\n=== PRUEBA COMPLETADA ===\n"; 