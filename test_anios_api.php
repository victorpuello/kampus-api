<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\Institucion;

// Simular el entorno de Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PRUEBA DE API DE AÑOS ACADÉMICOS ===\n\n";

// 1. Verificar que hay instituciones disponibles
echo "1. Verificando instituciones disponibles...\n";
$instituciones = Institucion::all();
echo "   Total de instituciones: " . $instituciones->count() . "\n";
foreach ($instituciones as $institucion) {
    echo "   - ID: {$institucion->id}, Nombre: {$institucion->nombre}, Siglas: {$institucion->siglas}\n";
}
echo "\n";

// 2. Verificar que hay usuarios disponibles
echo "2. Verificando usuarios disponibles...\n";
$users = User::all();
echo "   Total de usuarios: " . $users->count() . "\n";
if ($users->count() > 0) {
    $user = $users->first();
    echo "   Usuario de prueba: {$user->nombre} {$user->apellido} ({$user->email})\n";
}
echo "\n";

// 3. Verificar años académicos existentes
echo "3. Verificando años académicos existentes...\n";
$anios = \App\Models\Anio::with('institucion')->get();
echo "   Total de años académicos: " . $anios->count() . "\n";
foreach ($anios as $anio) {
    $institucionNombre = $anio->institucion ? $anio->institucion->nombre : 'Sin institución';
    echo "   - ID: {$anio->id}, Nombre: {$anio->nombre}, Institución: {$institucionNombre}, Estado: {$anio->estado}\n";
}
echo "\n";

// 4. Probar creación de un año académico
echo "4. Probando creación de año académico...\n";
try {
    $institucion = $instituciones->first();
    $anio = \App\Models\Anio::create([
        'nombre' => '2024-2025 Test',
        'fecha_inicio' => '2024-08-01',
        'fecha_fin' => '2025-06-30',
        'institucion_id' => $institucion->id,
        'estado' => 'activo'
    ]);
    echo "   ✅ Año académico creado exitosamente: {$anio->nombre}\n";
    
    // Verificar que se creó con la institución
    $anio->load('institucion');
    echo "   Institución asignada: {$anio->institucion->nombre}\n";
    
    // Eliminar el año de prueba
    $anio->delete();
    echo "   🗑️  Año de prueba eliminado\n";
    
} catch (Exception $e) {
    echo "   ❌ Error al crear año académico: " . $e->getMessage() . "\n";
}
echo "\n";

echo "=== PRUEBA COMPLETADA ===\n"; 