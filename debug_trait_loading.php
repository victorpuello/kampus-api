<?php

require_once 'vendor/autoload.php';

use App\Models\Institucion;
use App\Traits\HasFileUploads;

// Inicializar Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 DEBUG DE CARGA DEL TRAIT\n";
echo "===========================\n\n";

// Verificar si el trait está siendo usado
$institucion = new Institucion();

echo "📋 VERIFICACIÓN DE TRAITS:\n";
$traits = class_uses($institucion);
echo "   Traits usados: " . implode(', ', $traits) . "\n";
echo "   HasFileUploads está presente: " . (in_array(HasFileUploads::class, $traits) ? '✅ Sí' : '❌ No') . "\n\n";

// Verificar propiedades del trait
echo "📋 PROPIEDADES DEL TRAIT:\n";
$reflection = new ReflectionClass($institucion);
$properties = $reflection->getProperties();

foreach ($properties as $property) {
    if ($property->getName() === 'fileFields' || $property->getName() === 'filePaths') {
        echo "   Propiedad: " . $property->getName() . "\n";
        echo "   Declarada en: " . $property->getDeclaringClass()->getName() . "\n";
        echo "   Visibilidad: " . ($property->isPublic() ? 'public' : ($property->isProtected() ? 'protected' : 'private')) . "\n";
        echo "   Estática: " . ($property->isStatic() ? 'Sí' : 'No') . "\n\n";
    }
}

// Verificar métodos del trait
echo "📋 MÉTODOS DEL TRAIT:\n";
$methods = $reflection->getMethods();
foreach ($methods as $method) {
    if (strpos($method->getName(), 'File') !== false) {
        echo "   Método: " . $method->getName() . "\n";
        echo "   Declarado en: " . $method->getDeclaringClass()->getName() . "\n";
        echo "   Público: " . ($method->isPublic() ? 'Sí' : 'No') . "\n\n";
    }
}

// Probar acceso directo a las propiedades
echo "📋 ACCESO DIRECTO A PROPIEDADES:\n";
try {
    $reflection = new ReflectionClass($institucion);
    $fileFieldsProperty = $reflection->getProperty('fileFields');
    $fileFieldsProperty->setAccessible(true);
    $fileFieldsValue = $fileFieldsProperty->getValue($institucion);
    
    echo "   fileFields (acceso directo): " . var_export($fileFieldsValue, true) . "\n";
    
    $filePathsProperty = $reflection->getProperty('filePaths');
    $filePathsProperty->setAccessible(true);
    $filePathsValue = $filePathsProperty->getValue($institucion);
    
    echo "   filePaths (acceso directo): " . var_export($filePathsValue, true) . "\n\n";
    
} catch (Exception $e) {
    echo "   ❌ Error al acceder a propiedades: " . $e->getMessage() . "\n\n";
}

echo "🏁 DEBUG COMPLETADO\n"; 