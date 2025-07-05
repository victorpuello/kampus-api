<?php

require_once 'vendor/autoload.php';

use App\Models\Grado;
use App\Models\Institucion;

// Inicializar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFICACIÓN DE GRADOS CREADOS ===\n\n";

// 1. Total de grados
$totalGrados = Grado::count();
echo "📊 Total de grados en el sistema: {$totalGrados}\n\n";

// 2. Grados por nivel
echo "📚 Grados por nivel:\n";
$gradosPorNivel = Grado::selectRaw('nivel, count(*) as total')
    ->groupBy('nivel')
    ->orderBy('nivel')
    ->get();

foreach ($gradosPorNivel as $nivel) {
    echo "  {$nivel->nivel}: {$nivel->total} grados\n";
}
echo "\n";

// 3. Grados por institución
echo "🏫 Grados por institución:\n";
$instituciones = Institucion::withCount('grados')->get();
foreach ($instituciones as $institucion) {
    echo "  {$institucion->nombre}: {$institucion->grados_count} grados\n";
}
echo "\n";

// 4. Ejemplos de grados creados
echo "📋 Ejemplos de grados creados:\n";
$gradosEjemplo = Grado::with('institucion')
    ->orderBy('nivel')
    ->orderBy('nombre')
    ->limit(20)
    ->get();

foreach ($gradosEjemplo as $grado) {
    echo "  - {$grado->nombre} ({$grado->nivel}) - {$grado->institucion->nombre}\n";
}

echo "\n✅ Verificación completada exitosamente!\n"; 