<?php

require_once 'vendor/autoload.php';

use App\Models\Institucion;

// Inicializar Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 DEBUG DE CONFLICTO DE PROPIEDADES\n";
echo "===================================\n\n";

// Verificar todas las propiedades del modelo
$institucion = new Institucion();
$reflection = new ReflectionClass($institucion);
$properties = $reflection->getProperties();

echo "📋 TODAS LAS PROPIEDADES DEL MODELO:\n";
foreach ($properties as $property) {
    echo "   Propiedad: " . $property->getName() . "\n";
    echo "   Declarada en: " . $property->getDeclaringClass()->getName() . "\n";
    echo "   Visibilidad: " . ($property->isPublic() ? 'public' : ($property->isProtected() ? 'protected' : 'private')) . "\n";
    echo "   Estática: " . ($property->isStatic() ? 'Sí' : 'No') . "\n";
    
    // Intentar obtener el valor
    try {
        $property->setAccessible(true);
        $value = $property->getValue($institucion);
        echo "   Valor: " . var_export($value, true) . "\n";
    } catch (Exception $e) {
        echo "   Error al obtener valor: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

// Verificar si hay propiedades con nombres similares
echo "🔍 BUSCANDO PROPIEDADES SIMILARES:\n";
foreach ($properties as $property) {
    $name = $property->getName();
    if (strpos($name, 'file') !== false || strpos($name, 'File') !== false) {
        echo "   Propiedad relacionada con archivos: {$name}\n";
        echo "   Declarada en: " . $property->getDeclaringClass()->getName() . "\n";
        echo "   Visibilidad: " . ($property->isPublic() ? 'public' : ($property->isProtected() ? 'protected' : 'private')) . "\n";
        
        try {
            $property->setAccessible(true);
            $value = $property->getValue($institucion);
            echo "   Valor: " . var_export($value, true) . "\n";
        } catch (Exception $e) {
            echo "   Error al obtener valor: " . $e->getMessage() . "\n";
        }
        echo "\n";
    }
}

// Verificar si hay propiedades en el trait que no se están inicializando
echo "🔍 VERIFICANDO TRAIT HASFILEUPLOADS:\n";
$traitReflection = new ReflectionClass('App\Traits\HasFileUploads');
$traitProperties = $traitReflection->getProperties();

foreach ($traitProperties as $property) {
    echo "   Propiedad del trait: " . $property->getName() . "\n";
    echo "   Visibilidad: " . ($property->isPublic() ? 'public' : ($property->isProtected() ? 'protected' : 'private')) . "\n";
    echo "   Estática: " . ($property->isStatic() ? 'Sí' : 'No') . "\n\n";
}

echo "🏁 DEBUG COMPLETADO\n"; 