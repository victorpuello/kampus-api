<?php

require_once 'vendor/autoload.php';

use App\Models\Institucion;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

// Inicializar Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 DIAGNÓSTICO DE ACTUALIZACIÓN DE INSTITUCIONES\n";
echo "================================================\n\n";

// 1. Verificar si existe una institución para probar
$institucion = Institucion::first();

if (!$institucion) {
    echo "❌ No hay instituciones en la base de datos para probar\n";
    exit(1);
}

echo "✅ Institución encontrada para pruebas:\n";
echo "   ID: {$institucion->id}\n";
echo "   Nombre: {$institucion->nombre}\n";
echo "   Escudo actual: " . ($institucion->escudo ?: 'Ninguno') . "\n\n";

// 2. Verificar configuración del modelo
echo "🔧 CONFIGURACIÓN DEL MODELO:\n";
echo "   File Fields: " . implode(', ', $institucion->fileFields ?? []) . "\n";
echo "   File Paths: " . json_encode($institucion->filePaths ?? []) . "\n\n";

// 3. Verificar si el directorio de escudos existe
$escudosPath = 'storage/app/public/instituciones/escudos';
echo "📁 VERIFICACIÓN DE DIRECTORIOS:\n";
echo "   Directorio escudos existe: " . (is_dir($escudosPath) ? '✅ Sí' : '❌ No') . "\n";
echo "   Permisos de escritura: " . (is_writable($escudosPath) ? '✅ Sí' : '❌ No') . "\n\n";

// 4. Simular una actualización sin archivo
echo "🔄 PRUEBA DE ACTUALIZACIÓN SIN ARCHIVO:\n";
try {
    $dataOriginal = [
        'nombre' => $institucion->nombre,
        'siglas' => $institucion->siglas,
        'slogan' => $institucion->slogan,
        'dane' => $institucion->dane,
        'resolucion_aprobacion' => $institucion->resolucion_aprobacion,
        'direccion' => $institucion->direccion,
        'telefono' => $institucion->telefono,
        'email' => $institucion->email,
        'rector' => $institucion->rector,
    ];
    
    // Configurar campos de archivo
    $institucion->setFileFields(['escudo']);
    $institucion->setFilePaths(['escudo' => 'instituciones/escudos']);
    
    // Actualizar solo datos (sin archivo)
    $institucion->update($dataOriginal);
    
    echo "   ✅ Actualización sin archivo exitosa\n";
    echo "   Escudo después de actualización: " . ($institucion->escudo ?: 'Ninguno') . "\n\n";
    
} catch (Exception $e) {
    echo "   ❌ Error en actualización sin archivo: " . $e->getMessage() . "\n\n";
}

// 5. Verificar el método uploadFile directamente
echo "🔧 PRUEBA DEL MÉTODO UPLOADFILE:\n";
try {
    // Crear un archivo de prueba simulado
    $testImagePath = __DIR__ . '/test_image.jpg';
    
    // Crear una imagen de prueba si no existe
    if (!file_exists($testImagePath)) {
        $image = imagecreate(100, 100);
        $bgColor = imagecolorallocate($image, 255, 255, 255);
        $textColor = imagecolorallocate($image, 0, 0, 0);
        imagestring($image, 5, 10, 40, 'TEST', $textColor);
        imagejpeg($image, $testImagePath);
        imagedestroy($image);
    }
    
    // Crear UploadedFile simulado
    $uploadedFile = new UploadedFile(
        $testImagePath,
        'test_image.jpg',
        'image/jpeg',
        null,
        true
    );
    
    echo "   Archivo de prueba creado: {$testImagePath}\n";
    echo "   Tamaño del archivo: " . filesize($testImagePath) . " bytes\n";
    
    // Probar uploadFile
    $result = $institucion->uploadFile($uploadedFile, 'escudo', [
        'resize' => true,
        'width' => 300,
        'height' => 300,
        'quality' => 85
    ]);
    
    if ($result) {
        echo "   ✅ uploadFile exitoso\n";
        echo "   Nuevo escudo: " . ($institucion->escudo ?: 'Ninguno') . "\n";
        
        // Verificar si el archivo existe físicamente
        if ($institucion->escudo) {
            $fullPath = storage_path('app/public/' . $institucion->escudo);
            echo "   Archivo existe físicamente: " . (file_exists($fullPath) ? '✅ Sí' : '❌ No') . "\n";
            echo "   Ruta completa: {$fullPath}\n";
        }
    } else {
        echo "   ❌ uploadFile falló\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error en uploadFile: " . $e->getMessage() . "\n";
    echo "   Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n🏁 DIAGNÓSTICO COMPLETADO\n";

// Script para probar el endpoint de actualización de institución
$baseUrl = 'http://kampus.test/api/v1';

// Datos de prueba
$institutionId = 1;
$updateData = [
    'nombre' => 'Institución de Prueba Actualizada',
    'siglas' => 'IPA',
    'slogan' => 'Slogan de prueba actualizado',
    'email' => 'test@institucion.edu.co'
];

echo "🔍 Probando actualización de institución...\n";
echo "ID: $institutionId\n";
echo "Datos a actualizar: " . json_encode($updateData, JSON_PRETTY_PRINT) . "\n\n";

try {
    // 1. Obtener la institución actual
    echo "📥 Obteniendo institución actual...\n";
    $response = Http::get("$baseUrl/instituciones/$institutionId");
    
    if ($response->successful()) {
        $currentData = $response->json('data');
        echo "✅ Institución actual obtenida:\n";
        echo "   Nombre: " . $currentData['nombre'] . "\n";
        echo "   Siglas: " . $currentData['siglas'] . "\n";
        echo "   Slogan: " . ($currentData['slogan'] ?? 'N/A') . "\n\n";
    } else {
        echo "❌ Error al obtener institución: " . $response->status() . "\n";
        echo $response->body() . "\n";
        exit(1);
    }

    // 2. Actualizar la institución
    echo "🔄 Actualizando institución...\n";
    $updateResponse = Http::put("$baseUrl/instituciones/$institutionId", $updateData);
    
    if ($updateResponse->successful()) {
        $updatedData = $updateResponse->json('data');
        echo "✅ Institución actualizada exitosamente:\n";
        echo "   Nombre: " . $updatedData['nombre'] . "\n";
        echo "   Siglas: " . $updatedData['siglas'] . "\n";
        echo "   Slogan: " . ($updatedData['slogan'] ?? 'N/A') . "\n\n";
    } else {
        echo "❌ Error al actualizar institución: " . $updateResponse->status() . "\n";
        echo $updateResponse->body() . "\n";
        exit(1);
    }

    // 3. Verificar que los cambios se guardaron
    echo "🔍 Verificando cambios...\n";
    $verifyResponse = Http::get("$baseUrl/instituciones/$institutionId");
    
    if ($verifyResponse->successful()) {
        $verifiedData = $verifyResponse->json('data');
        echo "✅ Verificación completada:\n";
        echo "   Nombre: " . $verifiedData['nombre'] . "\n";
        echo "   Siglas: " . $verifiedData['siglas'] . "\n";
        echo "   Slogan: " . ($verifiedData['slogan'] ?? 'N/A') . "\n\n";
        
        // Verificar que los cambios se aplicaron
        $changesApplied = true;
        foreach ($updateData as $field => $value) {
            if ($verifiedData[$field] !== $value) {
                echo "❌ Campo '$field' no se actualizó correctamente\n";
                echo "   Esperado: $value\n";
                echo "   Actual: " . $verifiedData[$field] . "\n";
                $changesApplied = false;
            }
        }
        
        if ($changesApplied) {
            echo "🎉 ¡Todos los cambios se aplicaron correctamente!\n";
        } else {
            echo "❌ Algunos cambios no se aplicaron\n";
        }
    } else {
        echo "❌ Error al verificar cambios: " . $verifyResponse->status() . "\n";
        echo $verifyResponse->body() . "\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
} 