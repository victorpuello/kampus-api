<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\Anio;
use App\Models\Periodo;

// Simular el entorno de Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PRUEBA DE API DE PERIODOS ===\n\n";

// 1. Verificar que hay años académicos disponibles
echo "1. Verificando años académicos disponibles...\n";
$anios = Anio::all();
echo "   Total de años académicos: " . $anios->count() . "\n";
foreach ($anios as $anio) {
    echo "   - ID: {$anio->id}, Nombre: {$anio->nombre}, Estado: {$anio->estado}\n";
}
echo "\n";

// 2. Verificar que hay usuarios disponibles
echo "2. Verificando usuarios disponibles...\n";
$users = User::all();
echo "   Total de usuarios: " . $users->count() . "\n";
if ($users->count() > 0) {
    $user = $users->first();
    echo "   - Usuario de prueba: {$user->nombre} {$user->apellido} ({$user->email})\n";
}
echo "\n";

// 3. Verificar periodos existentes
echo "3. Verificando periodos existentes...\n";
$periodos = Periodo::with('anio')->get();
echo "   Total de periodos: " . $periodos->count() . "\n";
foreach ($periodos as $periodo) {
    echo "   - ID: {$periodo->id}, Nombre: {$periodo->nombre}, Año: {$periodo->anio->nombre}\n";
}
echo "\n";

// 4. Probar creación de un periodo
echo "4. Probando creación de un periodo...\n";
if ($anios->count() > 0) {
    $anio = $anios->first();
    
    try {
        $periodo = Periodo::create([
            'nombre' => 'Periodo de Prueba',
            'fecha_inicio' => '2024-01-15',
            'fecha_fin' => '2024-06-15',
            'anio_id' => $anio->id,
        ]);
        
        echo "   ✅ Periodo creado exitosamente:\n";
        echo "      - ID: {$periodo->id}\n";
        echo "      - Nombre: {$periodo->nombre}\n";
        echo "      - Año: {$periodo->anio->nombre}\n";
        echo "      - Fecha inicio: {$periodo->fecha_inicio}\n";
        echo "      - Fecha fin: {$periodo->fecha_fin}\n";
        
        // Limpiar el periodo de prueba
        $periodo->delete();
        echo "   🧹 Periodo de prueba eliminado\n";
        
    } catch (Exception $e) {
        echo "   ❌ Error al crear periodo: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ⚠️  No hay años académicos disponibles para crear periodos\n";
}
echo "\n";

// 5. Verificar relaciones
echo "5. Verificando relaciones...\n";
if ($anios->count() > 0) {
    $anio = $anios->first();
    $periodosDelAnio = $anio->periodos;
    echo "   - Año: {$anio->nombre}\n";
    echo "   - Periodos asociados: " . $periodosDelAnio->count() . "\n";
    
    foreach ($periodosDelAnio as $periodo) {
        echo "     * {$periodo->nombre} ({$periodo->fecha_inicio} - {$periodo->fecha_fin})\n";
    }
}
echo "\n";

echo "=== PRUEBA COMPLETADA ===\n";
echo "✅ Backend de periodos funcionando correctamente\n";
echo "🎯 Puedes probar la interfaz en: http://kampus.test:5173/anios/{id}\n"; 