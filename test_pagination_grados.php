<?php

require_once 'vendor/autoload.php';

use App\Models\Grado;

// Inicializar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PRUEBA DE PAGINACIÓN DE GRADOS ===\n\n";

// 1. Probar paginación básica
echo "📊 Paginación básica (10 por página):\n";
$gradosPagina1 = Grado::paginate(10, ['*'], 'page', 1);
echo "Página 1:\n";
echo "  Total: {$gradosPagina1->total()}\n";
echo "  Por página: {$gradosPagina1->perPage()}\n";
echo "  Páginas totales: {$gradosPagina1->lastPage()}\n";
echo "  Elementos en esta página: " . count($gradosPagina1->items()) . "\n";
echo "  Mostrando del {$gradosPagina1->firstItem()} al {$gradosPagina1->lastItem()}\n\n";

// 2. Probar segunda página
echo "📊 Segunda página:\n";
$gradosPagina2 = Grado::paginate(10, ['*'], 'page', 2);
echo "Página 2:\n";
echo "  Elementos en esta página: " . count($gradosPagina2->items()) . "\n";
echo "  Mostrando del {$gradosPagina2->firstItem()} al {$gradosPagina2->lastItem()}\n\n";

// 3. Probar con búsqueda
echo "📊 Paginación con búsqueda (buscar 'Grado'):\n";
$gradosConBusqueda = Grado::where('nombre', 'like', '%Grado%')->paginate(10, ['*'], 'page', 1);
echo "Resultados con búsqueda:\n";
echo "  Total: {$gradosConBusqueda->total()}\n";
echo "  Páginas totales: {$gradosConBusqueda->lastPage()}\n";
echo "  Elementos en esta página: " . count($gradosConBusqueda->items()) . "\n\n";

// 4. Probar diferentes tamaños de página
echo "📊 Diferentes tamaños de página:\n";
$tamanos = [5, 10, 25, 50];
foreach ($tamanos as $tamano) {
    $grados = Grado::paginate($tamano, ['*'], 'page', 1);
    echo "  {$tamano} por página: {$grados->total()} total, {$grados->lastPage()} páginas\n";
}
echo "\n";

// 5. Probar con filtro de institución
echo "📊 Paginación con filtro de institución (ID 1):\n";
$gradosInstitucion = Grado::where('institucion_id', 1)->paginate(10, ['*'], 'page', 1);
echo "Grados de institución 1:\n";
echo "  Total: {$gradosInstitucion->total()}\n";
echo "  Páginas totales: {$gradosInstitucion->lastPage()}\n";
echo "  Elementos en esta página: " . count($gradosInstitucion->items()) . "\n\n";

// 6. Mostrar algunos grados de ejemplo
echo "📋 Ejemplos de grados (página 1):\n";
foreach ($gradosPagina1->items() as $grado) {
    echo "  - {$grado->nombre} ({$grado->nivel}) - Institución {$grado->institucion_id}\n";
}

echo "\n✅ Prueba de paginación completada!\n"; 