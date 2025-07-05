<?php

require_once 'vendor/autoload.php';

// Simular entorno Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PRUEBA DE ACCESO A RUTAS API ===\n\n";

// 1. Verificar entorno
echo "1️⃣ Verificando entorno...\n";
echo "APP_ENV: " . config('app.env', 'no definido') . "\n";
echo "APP_DEBUG: " . (config('app.debug', false) ? 'true' : 'false') . "\n";
$env = $_ENV['APP_ENV'] ?? 'production';
echo "Es desarrollo: " . (in_array($env, ['local', 'development']) ? '✅ Sí' : '❌ No') . "\n\n";

// 2. Verificar configuración de rutas
echo "2️⃣ Verificando configuración de rutas...\n";
$router = app('router');
$routes = $router->getRoutes();

echo "Rutas registradas:\n";
foreach ($routes as $route) {
    if (str_contains($route->uri(), 'api/v1')) {
        $methods = implode('|', $route->methods());
        $middleware = implode(',', $route->middleware());
        echo "- {$methods} {$route->uri()} [{$middleware}]\n";
    }
}
echo "\n";

// 3. Probar acceso directo a una ruta protegida
echo "3️⃣ Probando acceso directo a ruta protegida...\n";

try {
    // Simular una petición HTTP
    $request = \Illuminate\Http\Request::create('/api/v1/instituciones', 'GET');
    $request->headers->set('Accept', 'application/json');
    
    // Ejecutar la petición
    $response = app()->handle($request);
    
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Contenido: " . substr($response->getContent(), 0, 200) . "...\n";
    
    if ($response->getStatusCode() === 200) {
        echo "✅ Acceso exitoso - La autenticación está desactivada\n";
    } else {
        echo "❌ Acceso denegado - Status: " . $response->getStatusCode() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DE LA PRUEBA ===\n"; 