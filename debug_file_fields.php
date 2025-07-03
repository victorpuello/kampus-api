<?php

require_once 'vendor/autoload.php';

use App\Models\Institucion;

// Inicializar Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 DEBUG DE PROPIEDADES FILEFIELDS\n";
echo "==================================\n\n";

// Crear una nueva instancia de institución
$institucion = new Institucion();

echo "📋 PROPIEDADES INICIALES:\n";
echo "   fileFields type: " . gettype($institucion->fileFields ?? 'undefined') . "\n";
echo "   fileFields value: " . var_export($institucion->fileFields ?? 'undefined', true) . "\n";
echo "   filePaths type: " . gettype($institucion->filePaths ?? 'undefined') . "\n";
echo "   filePaths value: " . var_export($institucion->filePaths ?? 'undefined', true) . "\n\n";

// Configurar campos
echo "🔧 CONFIGURANDO CAMPOS:\n";
$institucion->setFileFields(['escudo']);
$institucion->setFilePaths(['escudo' => 'instituciones/escudos']);

echo "   Después de setFileFields:\n";
echo "   fileFields type: " . gettype($institucion->fileFields) . "\n";
echo "   fileFields value: " . var_export($institucion->fileFields, true) . "\n";
echo "   filePaths type: " . gettype($institucion->filePaths) . "\n";
echo "   filePaths value: " . var_export($institucion->filePaths, true) . "\n\n";

// Probar con una institución existente
echo "📋 PROPIEDADES DE INSTANCIA EXISTENTE:\n";
$existingInstitucion = Institucion::first();

if ($existingInstitucion) {
    echo "   Institución ID: {$existingInstitucion->id}\n";
    echo "   fileFields type: " . gettype($existingInstitucion->fileFields ?? 'undefined') . "\n";
    echo "   fileFields value: " . var_export($existingInstitucion->fileFields ?? 'undefined', true) . "\n";
    echo "   filePaths type: " . gettype($existingInstitucion->filePaths ?? 'undefined') . "\n";
    echo "   filePaths value: " . var_export($existingInstitucion->filePaths ?? 'undefined', true) . "\n\n";
    
    echo "🔧 CONFIGURANDO CAMPOS EN INSTANCIA EXISTENTE:\n";
    $existingInstitucion->setFileFields(['escudo']);
    $existingInstitucion->setFilePaths(['escudo' => 'instituciones/escudos']);
    
    echo "   Después de setFileFields:\n";
    echo "   fileFields type: " . gettype($existingInstitucion->fileFields) . "\n";
    echo "   fileFields value: " . var_export($existingInstitucion->fileFields, true) . "\n";
    echo "   filePaths type: " . gettype($existingInstitucion->filePaths) . "\n";
    echo "   filePaths value: " . var_export($existingInstitucion->filePaths, true) . "\n\n";
    
    // Verificar si el campo escudo está en fileFields
    echo "🔍 VERIFICACIÓN DE CAMPOS:\n";
    echo "   'escudo' está en fileFields: " . (in_array('escudo', $existingInstitucion->fileFields) ? '✅ Sí' : '❌ No') . "\n";
    echo "   'escudo' está en filePaths: " . (isset($existingInstitucion->filePaths['escudo']) ? '✅ Sí' : '❌ No') . "\n";
    
} else {
    echo "❌ No hay instituciones en la base de datos\n";
}

echo "\n🏁 DEBUG COMPLETADO\n"; 