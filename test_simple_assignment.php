<?php

require_once 'vendor/autoload.php';

use App\Models\Institucion;

// Inicializar Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 PRUEBA SIMPLE DE ASIGNACIÓN\n";
echo "=============================\n\n";

// Crear una nueva instancia
$institucion = new Institucion();

echo "📋 ESTADO INICIAL:\n";
echo "   fileFields: " . var_export($institucion->fileFields ?? 'undefined', true) . "\n";
echo "   filePaths: " . var_export($institucion->filePaths ?? 'undefined', true) . "\n\n";

// Asignación directa
echo "🔧 ASIGNACIÓN DIRECTA:\n";
$institucion->fileFields = ['escudo'];
$institucion->filePaths = ['escudo' => 'instituciones/escudos'];

echo "   Después de asignación directa:\n";
echo "   fileFields: " . var_export($institucion->fileFields, true) . "\n";
echo "   filePaths: " . var_export($institucion->filePaths, true) . "\n\n";

// Verificar si los campos están configurados
echo "🔍 VERIFICACIÓN DE CAMPOS:\n";
echo "   'escudo' está en fileFields: " . (is_array($institucion->fileFields) && in_array('escudo', $institucion->fileFields) ? '✅ Sí' : '❌ No') . "\n";
echo "   'escudo' está en filePaths: " . (is_array($institucion->filePaths) && isset($institucion->filePaths['escudo']) ? '✅ Sí' : '❌ No') . "\n\n";

// Probar métodos del trait
echo "🔧 PROBANDO MÉTODOS DEL TRAIT:\n";
$institucion->setFileFields(['escudo']);
$institucion->setFilePaths(['escudo' => 'instituciones/escudos']);

echo "   Después de métodos del trait:\n";
echo "   fileFields: " . var_export($institucion->fileFields, true) . "\n";
echo "   filePaths: " . var_export($institucion->filePaths, true) . "\n\n";

echo "🏁 PRUEBA COMPLETADA\n"; 