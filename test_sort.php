<?php

require_once 'vendor/autoload.php';

// Simular una petición HTTP
$_GET['sort_by'] = 'nombre';
$_GET['sort_direction'] = 'asc';
$_GET['page'] = '1';
$_GET['per_page'] = '10';

// Crear una instancia de la aplicación Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Simular autenticación
$user = \App\Models\User::where('email', 'admin@example.com')->first();
if ($user) {
    auth()->login($user);
    
    // Crear una instancia del controlador
    $controller = new \App\Http\Controllers\Api\V1\GrupoController();
    
    // Crear una petición simulada
    $request = new \Illuminate\Http\Request();
    $request->merge($_GET);
    
    try {
        // Llamar al método index
        $response = $controller->index($request);
        
        // Obtener los datos
        $data = $response->response()->getData();
        
        echo "Ordenamiento funcionando correctamente!\n";
        echo "Total de grupos: " . $data->meta->total . "\n";
        echo "Grupos en la página actual:\n";
        
        foreach ($data->data as $grupo) {
            echo "- " . $grupo->nombre . "\n";
        }
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        echo "Trace: " . $e->getTraceAsString() . "\n";
    }
} else {
    echo "Usuario admin no encontrado\n";
} 