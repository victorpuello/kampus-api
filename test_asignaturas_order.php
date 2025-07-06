<?php

require_once 'vendor/autoload.php';

// Cargar Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Prueba de Ordenamiento de Asignaturas ===\n";

try {
    // Simular una petición con ordenamiento
    $request = new Illuminate\Http\Request();
    $request->merge([
        'sort_by' => 'nombre',
        'sort_direction' => 'asc',
        'per_page' => 5
    ]);

    // Crear una instancia del controlador
    $controller = new App\Http\Controllers\Api\V1\AsignaturaController();
    
    // Simular autenticación
    $user = App\Models\User::where('email', 'admin@example.com')->first();
    if (!$user) {
        echo "❌ Usuario admin no encontrado\n";
        exit(1);
    }
    
    // Autenticar al usuario
    auth()->login($user);
    echo "✅ Usuario autenticado: {$user->email}\n";
    
    // Ejecutar el método index
    $response = $controller->index($request);
    
    echo "✅ Respuesta exitosa\n";
    echo "Tipo de respuesta: " . get_class($response) . "\n";
    
    if ($response instanceof Illuminate\Http\Resources\Json\AnonymousResourceCollection) {
        $data = $response->response()->getData(true);
        echo "Estructura de respuesta:\n";
        print_r($data);
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
} 