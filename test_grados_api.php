<?php

require_once 'vendor/autoload.php';

use App\Models\Grado;
use App\Models\Institucion;

// Inicializar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PRUEBA DE API DE GRADOS ===\n\n";

// 1. Probar sin filtro de institución
echo "📊 Grados sin filtro de institución:\n";
$gradosSinFiltro = Grado::paginate(10);
echo "Total: {$gradosSinFiltro->total()}\n";
echo "Por página: {$gradosSinFiltro->perPage()}\n";
echo "Páginas: {$gradosSinFiltro->lastPage()}\n";
echo "Mostrando: " . count($gradosSinFiltro->items()) . " grados\n\n";

// 2. Probar con filtro de institución (ID 1)
echo "📊 Grados con filtro de institución (ID 1):\n";
$gradosConFiltro = Grado::where('institucion_id', 1)->paginate(100);
echo "Total: {$gradosConFiltro->total()}\n";
echo "Por página: {$gradosConFiltro->perPage()}\n";
echo "Páginas: {$gradosConFiltro->lastPage()}\n";
echo "Mostrando: " . count($gradosConFiltro->items()) . " grados\n\n";

// 3. Mostrar los grados de la institución 1
echo "📋 Grados de la institución 1:\n";
foreach ($gradosConFiltro->items() as $grado) {
    echo "  - {$grado->nombre} ({$grado->nivel})\n";
}

// 4. Verificar que se muestran todos los niveles
echo "\n📚 Niveles disponibles en institución 1:\n";
$niveles = $gradosConFiltro->pluck('nivel')->unique()->sort();
foreach ($niveles as $nivel) {
    $count = $gradosConFiltro->where('nivel', $nivel)->count();
    echo "  - {$nivel}: {$count} grados\n";
}

echo "\n✅ Prueba completada!\n"; 