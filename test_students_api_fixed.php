<?php
/**
 * Script de prueba para verificar la API de estudiantes
 * Después de las correcciones en StudentResource y GrupoResource
 */

require_once 'vendor/autoload.php';

// Inicializar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;

// Configurar la URL base
$baseUrl = 'http://127.0.0.1:8000';

echo "=== PRUEBA DE API DE ESTUDIANTES (CORREGIDA) ===\n\n";

// 1. Probar login para obtener token
echo "1. Iniciando sesión...\n";
$loginResponse = Http::post($baseUrl . '/api/v1/auth/login', [
    'email' => 'admin@example.com',
    'password' => '123456'
]);

if ($loginResponse->successful()) {
    $loginData = $loginResponse->json();
    $token = $loginData['data']['token'];
    echo "✓ Login exitoso. Token obtenido.\n\n";
} else {
    echo "✗ Error en login: " . $loginResponse->status() . "\n";
    echo $loginResponse->body() . "\n\n";
    exit(1);
}

// 2. Probar obtener lista de estudiantes
echo "2. Obteniendo lista de estudiantes...\n";
$studentsResponse = Http::withHeaders([
    'Authorization' => 'Bearer ' . $token,
    'Accept' => 'application/json',
    'Content-Type' => 'application/json'
])->get($baseUrl . '/api/v1/estudiantes');

if ($studentsResponse->successful()) {
    $studentsData = $studentsResponse->json();
    echo "✓ Lista de estudiantes obtenida exitosamente.\n";
    echo "Total de estudiantes: " . count($studentsData['data']) . "\n";
    
    if (count($studentsData['data']) > 0) {
        $firstStudent = $studentsData['data'][0];
        echo "\nPrimer estudiante:\n";
        echo "- ID: " . $firstStudent['id'] . "\n";
        echo "- Código: " . $firstStudent['codigo_estudiantil'] . "\n";
        echo "- Estado: " . $firstStudent['estado'] . "\n";
        
        // Verificar que user esté presente
        if (isset($firstStudent['user'])) {
            echo "- Usuario: " . $firstStudent['user']['nombre'] . " " . $firstStudent['user']['apellido'] . "\n";
            echo "- Email: " . $firstStudent['user']['email'] . "\n";
        } else {
            echo "- Usuario: NO PRESENTE (ERROR)\n";
        }
        
        // Verificar que grupo esté presente
        if (isset($firstStudent['grupo'])) {
            echo "- Grupo: " . $firstStudent['grupo']['nombre'] . "\n";
            if (isset($firstStudent['grupo']['sede'])) {
                echo "- Sede: " . $firstStudent['grupo']['sede']['nombre'] . "\n";
                if (isset($firstStudent['grupo']['sede']['institucion'])) {
                    echo "- Institución: " . $firstStudent['grupo']['sede']['institucion']['nombre'] . "\n";
                }
            }
            if (isset($firstStudent['grupo']['grado'])) {
                echo "- Grado: " . $firstStudent['grupo']['grado']['nombre'] . " (" . $firstStudent['grupo']['grado']['nivel'] . ")\n";
            }
        } else {
            echo "- Grupo: NO PRESENTE\n";
        }
        
        // Verificar que institucion esté presente (accessor)
        if (isset($firstStudent['institucion'])) {
            echo "- Institución (accessor): " . $firstStudent['institucion']['nombre'] . "\n";
        } else {
            echo "- Institución (accessor): NO PRESENTE\n";
        }
    }
    
    echo "\nEstructura completa del primer estudiante:\n";
    echo json_encode($firstStudent, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    
} else {
    echo "✗ Error al obtener estudiantes: " . $studentsResponse->status() . "\n";
    echo $studentsResponse->body() . "\n\n";
}

// 3. Probar obtener un estudiante específico
echo "\n3. Obteniendo estudiante específico...\n";
if (isset($firstStudent)) {
    $studentId = $firstStudent['id'];
    $studentResponse = Http::withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
        'Content-Type' => 'application/json'
    ])->get($baseUrl . '/api/v1/estudiantes/' . $studentId);

    if ($studentResponse->successful()) {
        $studentData = $studentResponse->json();
        echo "✓ Estudiante específico obtenido exitosamente.\n";
        echo "ID: " . $studentData['data']['id'] . "\n";
        echo "Nombre: " . $studentData['data']['user']['nombre'] . " " . $studentData['data']['user']['apellido'] . "\n";
    } else {
        echo "✗ Error al obtener estudiante específico: " . $studentResponse->status() . "\n";
        echo $studentResponse->body() . "\n";
    }
}

echo "\n=== PRUEBA COMPLETADA ===\n"; 