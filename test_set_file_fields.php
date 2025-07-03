<?php

require_once 'vendor/autoload.php';

use App\Models\Institucion;

// Inicializar Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 PRUEBA DE SETFILEFIELDS Y SETFILEPATHS\n";
echo "=========================================\n\n";

// Crear una nueva instancia
$institucion = new Institucion();

echo "📋 ESTADO INICIAL:\n";
echo "   fileFields: " . var_export($institucion->fileFields ?? 'undefined', true) . "\n";
echo "   filePaths: " . var_export($institucion->filePaths ?? 'undefined', true) . "\n\n";

// Probar setFileFields
echo "🔧 PROBANDO SETFILEFIELDS:\n";
try {
    $institucion->setFileFields(['escudo']);
    echo "   ✅ setFileFields ejecutado sin errores\n";
    echo "   fileFields después: " . var_export($institucion->fileFields, true) . "\n";
} catch (Exception $e) {
    echo "   ❌ Error en setFileFields: " . $e->getMessage() . "\n";
}

// Probar setFilePaths
echo "\n🔧 PROBANDO SETFILEPATHS:\n";
try {
    $institucion->setFilePaths(['escudo' => 'instituciones/escudos']);
    echo "   ✅ setFilePaths ejecutado sin errores\n";
    echo "   filePaths después: " . var_export($institucion->filePaths, true) . "\n";
} catch (Exception $e) {
    echo "   ❌ Error en setFilePaths: " . $e->getMessage() . "\n";
}

// Verificar estado final
echo "\n📋 ESTADO FINAL:\n";
echo "   fileFields: " . var_export($institucion->fileFields, true) . "\n";
echo "   filePaths: " . var_export($institucion->filePaths, true) . "\n";

// Verificar si los campos están configurados correctamente
echo "\n🔍 VERIFICACIÓN DE CAMPOS:\n";
echo "   'escudo' está en fileFields: " . (is_array($institucion->fileFields) && in_array('escudo', $institucion->fileFields) ? '✅ Sí' : '❌ No') . "\n";
echo "   'escudo' está en filePaths: " . (is_array($institucion->filePaths) && isset($institucion->filePaths['escudo']) ? '✅ Sí' : '❌ No') . "\n";

// Probar con una instancia existente
echo "\n📋 PRUEBA CON INSTANCIA EXISTENTE:\n";
$existingInstitucion = Institucion::first();

if ($existingInstitucion) {
    echo "   Institución ID: {$existingInstitucion->id}\n";
    echo "   fileFields inicial: " . var_export($existingInstitucion->fileFields ?? 'undefined', true) . "\n";
    
    $existingInstitucion->setFileFields(['escudo']);
    $existingInstitucion->setFilePaths(['escudo' => 'instituciones/escudos']);
    
    echo "   fileFields después: " . var_export($existingInstitucion->fileFields, true) . "\n";
    echo "   filePaths después: " . var_export($existingInstitucion->filePaths, true) . "\n";
}

echo "\n🏁 PRUEBA COMPLETADA\n"; 