<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Institucion;

echo "Instituciones en la base de datos:\n";
echo "Total: " . Institucion::count() . "\n\n";

$instituciones = Institucion::select('id', 'nombre', 'siglas')->get();

foreach ($instituciones as $institucion) {
    echo "ID: {$institucion->id} - Nombre: {$institucion->nombre} - Siglas: {$institucion->siglas}\n";
}

echo "\n"; 