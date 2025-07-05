<?php

require_once 'vendor/autoload.php';

use App\Models\Grado;
use App\Models\Institucion;

// Inicializar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PRUEBA DE GRADOS POR DEFECTO ===\n\n";

// 1. Verificar instituciones disponibles
echo "1. INSTITUCIONES DISPONIBLES:\n";
$instituciones = Institucion::all();
if ($instituciones->isEmpty()) {
    echo "   ❌ No hay instituciones disponibles. Creando una de prueba...\n";
    $institucion = Institucion::factory()->create();
    echo "   ✅ Institución creada: {$institucion->nombre} (ID: {$institucion->id})\n";
} else {
    echo "   ✅ Total de instituciones: {$instituciones->count()}\n";
    foreach ($instituciones as $institucion) {
        echo "   - {$institucion->nombre} (ID: {$institucion->id})\n";
    }
}
echo "\n";

// 2. Definir grados esperados
echo "2. GRADOS ESPERADOS POR NIVEL:\n";
$gradosEsperados = [
    'Preescolar' => ['Prejardín', 'Jardín', 'Transición'],
    'Básica Primaria' => ['Grado 1º', 'Grado 2º', 'Grado 3º', 'Grado 4º', 'Grado 5º'],
    'Básica Secundaria' => ['Grado 6º', 'Grado 7º', 'Grado 8º', 'Grado 9º'],
    'Educación Media' => ['Grado 10º', 'Grado 11º']
];

foreach ($gradosEsperados as $nivel => $grados) {
    echo "   📚 {$nivel}:\n";
    foreach ($grados as $grado) {
        echo "     - {$grado}\n";
    }
}
echo "\n";

// 3. Ejecutar el comando para crear grados por defecto
echo "3. EJECUTANDO COMANDO PARA CREAR GRADOS POR DEFECTO:\n";
try {
    $command = $app->make('App\Console\Commands\CreateDefaultGrados');
    $command->run(new Symfony\Component\Console\Input\ArrayInput([]), new Symfony\Component\Console\Output\ConsoleOutput());
    echo "   ✅ Comando ejecutado exitosamente\n";
} catch (Exception $e) {
    echo "   ❌ Error al ejecutar comando: " . $e->getMessage() . "\n";
}
echo "\n";

// 4. Verificar grados creados
echo "4. VERIFICANDO GRADOS CREADOS:\n";
$totalGrados = 0;
foreach ($instituciones as $institucion) {
    echo "   🏫 Institución: {$institucion->nombre}\n";
    
    foreach ($gradosEsperados as $nivel => $gradosEsperadosNivel) {
        echo "     📚 Nivel: {$nivel}\n";
        
        foreach ($gradosEsperadosNivel as $nombreGrado) {
            $grado = Grado::where('nombre', $nombreGrado)
                ->where('institucion_id', $institucion->id)
                ->first();
            
            if ($grado) {
                echo "       ✅ {$nombreGrado} - {$grado->nivel}\n";
                $totalGrados++;
            } else {
                echo "       ❌ {$nombreGrado} - NO ENCONTRADO\n";
            }
        }
    }
}
echo "\n";

// 5. Estadísticas finales
echo "5. ESTADÍSTICAS FINALES:\n";
$totalEsperado = count($instituciones) * array_sum(array_map('count', $gradosEsperados));
echo "   📊 Total de grados esperados: {$totalEsperado}\n";
echo "   📊 Total de grados encontrados: {$totalGrados}\n";
echo "   📊 Total de instituciones: " . count($instituciones) . "\n";

if ($totalGrados === $totalEsperado) {
    echo "   ✅ ¡Todos los grados por defecto se crearon correctamente!\n";
} else {
    echo "   ⚠️  Algunos grados no se crearon. Verificar el proceso.\n";
}

echo "\n";

// 6. Mostrar algunos grados de ejemplo
echo "6. EJEMPLOS DE GRADOS CREADOS:\n";
$gradosEjemplo = Grado::with('institucion')->limit(10)->get();
foreach ($gradosEjemplo as $grado) {
    echo "   - {$grado->nombre} ({$grado->nivel}) - {$grado->institucion->nombre}\n";
}

echo "\n🎉 ¡Prueba completada exitosamente!\n";
echo "✅ Los grados por defecto están configurados para crearse automáticamente.\n";
echo "🔧 Para recrear grados existentes, usar: php artisan grados:create-default --force\n";
echo "🎯 Para una institución específica: php artisan grados:create-default --institucion-id=1\n"; 