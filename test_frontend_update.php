<?php

require_once 'vendor/autoload.php';

use App\Models\Institucion;
use Illuminate\Http\UploadedFile;

// Inicializar Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 SIMULACIÓN DE ACTUALIZACIÓN DESDE FRONTEND\n";
echo "=============================================\n\n";

// 1. Obtener una institución existente
$institucion = Institucion::first();

if (!$institucion) {
    echo "❌ No hay instituciones para probar\n";
    exit(1);
}

echo "✅ Institución encontrada:\n";
echo "   ID: {$institucion->id}\n";
echo "   Nombre: {$institucion->nombre}\n";
echo "   Escudo actual: " . ($institucion->escudo ?: 'Ninguno') . "\n\n";

// 2. Crear una imagen de prueba
$testImagePath = __DIR__ . '/test_update.jpg';
if (!file_exists($testImagePath)) {
    $image = imagecreate(200, 200);
    $bgColor = imagecolorallocate($image, 100, 150, 200);
    $textColor = imagecolorallocate($image, 255, 255, 255);
    imagestring($image, 5, 50, 90, 'UPDATE', $textColor);
    imagejpeg($image, $testImagePath, 90);
    imagedestroy($image);
}

echo "📁 Imagen de prueba creada: {$testImagePath}\n";

// 3. Simular la petición del frontend
echo "\n🔄 SIMULANDO PETICIÓN DE ACTUALIZACIÓN:\n";

try {
    // Crear UploadedFile como lo haría el frontend
    $uploadedFile = new UploadedFile(
        $testImagePath,
        'test_update.jpg',
        'image/jpeg',
        null,
        true
    );
    
    // Simular datos de validación
    $data = [
        'nombre' => $institucion->nombre . ' (Actualizada)',
        'siglas' => $institucion->siglas,
        'slogan' => $institucion->slogan,
        'dane' => $institucion->dane,
        'resolucion_aprobacion' => $institucion->resolucion_aprobacion,
        'direccion' => $institucion->direccion,
        'telefono' => $institucion->telefono,
        'email' => $institucion->email,
        'rector' => $institucion->rector,
    ];
    
    echo "   Datos a actualizar: " . json_encode($data) . "\n";
    echo "   Archivo incluido: Sí\n";
    
    // Simular el flujo del controlador
    echo "\n   🔧 Configurando campos de archivo...\n";
    $institucion->setFileFields(['escudo']);
    $institucion->setFilePaths(['escudo' => 'instituciones/escudos']);
    
    // Mostrar tipo real antes de imprimir
    echo "   Tipo de fileFields: " . gettype($institucion->fileFields) . "\n";
    echo "   Tipo de filePaths: " . gettype($institucion->filePaths) . "\n";
    
    // Solo imprimir si son arrays
    if (is_array($institucion->fileFields)) {
        echo "   File Fields: " . implode(', ', $institucion->fileFields) . "\n";
    } else {
        echo "   File Fields: (no es array) " . var_export($institucion->fileFields, true) . "\n";
    }
    if (is_array($institucion->filePaths)) {
        echo "   File Paths: " . json_encode($institucion->filePaths) . "\n";
    } else {
        echo "   File Paths: (no es array) " . var_export($institucion->filePaths, true) . "\n";
    }
    
    // Probar uploadFile
    echo "\n   📤 Subiendo archivo...\n";
    $result = $institucion->uploadFile($uploadedFile, 'escudo', [
        'resize' => true,
        'width' => 300,
        'height' => 300,
        'quality' => 85
    ]);
    
    if ($result) {
        echo "   ✅ Archivo subido exitosamente\n";
        echo "   Nuevo escudo: " . ($institucion->escudo ?: 'Ninguno') . "\n";
        
        // Verificar archivo físico
        if ($institucion->escudo) {
            $fullPath = storage_path('app/public/' . $institucion->escudo);
            echo "   Archivo físico existe: " . (file_exists($fullPath) ? '✅ Sí' : '❌ No') . "\n";
            echo "   Tamaño: " . (file_exists($fullPath) ? filesize($fullPath) . ' bytes' : 'N/A') . "\n";
        }
        
        // Remover escudo de datos
        unset($data['escudo']);
        echo "\n   🔄 Actualizando otros campos...\n";
        
        // Actualizar otros campos
        $institucion->update($data);
        
        echo "   ✅ Actualización completa exitosa\n";
        echo "   Nombre final: {$institucion->nombre}\n";
        echo "   Escudo final: " . ($institucion->escudo ?: 'Ninguno') . "\n";
        
    } else {
        echo "   ❌ Error al subir archivo\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error en la actualización: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n🏁 SIMULACIÓN COMPLETADA\n"; 