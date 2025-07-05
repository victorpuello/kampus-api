<?php

require_once 'vendor/autoload.php';

use App\Models\Institucion;
use App\Services\FileUploadService;
use Illuminate\Http\UploadedFile;

// Simular entorno Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PRUEBA DE CARGA DE IMÁGENES ===\n\n";

try {
    // 1. Verificar configuración de storage
    echo "1️⃣ Verificando configuración de storage...\n";
    
    $storagePath = storage_path('app/public');
    $publicPath = public_path('storage');
    
    echo "Storage path: {$storagePath}\n";
    echo "Public path: {$publicPath}\n";
    echo "Storage existe: " . (is_dir($storagePath) ? '✅' : '❌') . "\n";
    echo "Public link existe: " . (is_link($publicPath) ? '✅' : '❌') . "\n";
    
    // 2. Verificar directorios
    echo "\n2️⃣ Verificando directorios...\n";
    
    $institucionesPath = $storagePath . '/instituciones';
    $escudosPath = $institucionesPath . '/escudos';
    
    echo "Directorio instituciones: " . (is_dir($institucionesPath) ? '✅' : '❌') . "\n";
    echo "Directorio escudos: " . (is_dir($escudosPath) ? '✅' : '❌') . "\n";
    
    // 3. Verificar permisos
    echo "\n3️⃣ Verificando permisos...\n";
    
    echo "Storage writable: " . (is_writable($storagePath) ? '✅' : '❌') . "\n";
    echo "Instituciones writable: " . (is_writable($institucionesPath) ? '✅' : '❌') . "\n";
    echo "Escudos writable: " . (is_writable($escudosPath) ? '✅' : '❌') . "\n";
    
    // 4. Probar servicio de carga
    echo "\n4️⃣ Probando servicio de carga...\n";
    
    $fileService = app(FileUploadService::class);
    echo "FileUploadService cargado: ✅\n";
    
    // 5. Crear archivo de prueba
    echo "\n5️⃣ Creando archivo de prueba...\n";
    
    $testImagePath = __DIR__ . '/test_image.png';
    
    // Crear una imagen de prueba simple
    $image = imagecreate(200, 200);
    $bgColor = imagecolorallocate($image, 255, 255, 255);
    $textColor = imagecolorallocate($image, 0, 0, 0);
    imagestring($image, 5, 50, 90, 'TEST IMAGE', $textColor);
    imagepng($image, $testImagePath);
    imagedestroy($image);
    
    echo "Imagen de prueba creada: ✅\n";
    
    // 6. Crear UploadedFile
    echo "\n6️⃣ Creando UploadedFile...\n";
    
    $uploadedFile = new UploadedFile(
        $testImagePath,
        'test_image.png',
        'image/png',
        null,
        true
    );
    
    echo "UploadedFile creado: ✅\n";
    echo "Tamaño: " . $uploadedFile->getSize() . " bytes\n";
    echo "MIME: " . $uploadedFile->getMimeType() . "\n";
    
    // 7. Probar carga directa con FileUploadService
    echo "\n7️⃣ Probando carga directa con FileUploadService...\n";
    
    try {
        $result = $fileService->uploadImage($uploadedFile, 'instituciones/escudos', [
            'resize' => true,
            'width' => 300,
            'height' => 300,
            'quality' => 85
        ]);
        
        echo "Carga directa: ✅\n";
        echo "Ruta guardada: " . $result['path'] . "\n";
        echo "URL: " . $result['url'] . "\n";
        
        // Verificar que el archivo existe
        $fullPath = storage_path('app/public/' . $result['path']);
        echo "Archivo existe: " . (file_exists($fullPath) ? '✅' : '❌') . "\n";
        
    } catch (Exception $e) {
        echo "Error en carga directa: ❌\n";
        echo "Mensaje: " . $e->getMessage() . "\n";
        echo "Archivo: " . $e->getFile() . "\n";
        echo "Línea: " . $e->getLine() . "\n";
    }
    
    // 8. Probar carga con modelo
    echo "\n8️⃣ Probando carga con modelo...\n";
    
    try {
        // Buscar una institución existente o crear una de prueba
        $institucion = Institucion::first();
        
        if (!$institucion) {
            echo "No hay instituciones en la base de datos\n";
        } else {
            echo "Institución encontrada: " . $institucion->nombre . "\n";
            
            // Configurar campos de archivo
            $institucion->setFileFields(['escudo']);
            $institucion->setFilePaths(['escudo' => 'instituciones/escudos']);
            
            // Probar carga
            $success = $institucion->uploadFile($uploadedFile, 'escudo', [
                'resize' => true,
                'width' => 300,
                'height' => 300,
                'quality' => 85
            ]);
            
            echo "Carga con modelo: " . ($success ? '✅' : '❌') . "\n";
            
            if ($success) {
                echo "Campo escudo actualizado: " . $institucion->escudo . "\n";
                echo "URL del archivo: " . $institucion->getFileUrl('escudo') . "\n";
            }
        }
        
    } catch (Exception $e) {
        echo "Error con modelo: ❌\n";
        echo "Mensaje: " . $e->getMessage() . "\n";
        echo "Archivo: " . $e->getFile() . "\n";
        echo "Línea: " . $e->getLine() . "\n";
    }
    
    // 9. Limpiar archivo de prueba
    echo "\n9️⃣ Limpiando archivo de prueba...\n";
    
    if (file_exists($testImagePath)) {
        unlink($testImagePath);
        echo "Archivo de prueba eliminado: ✅\n";
    }
    
} catch (Exception $e) {
    echo "Error general: ❌\n";
    echo "Mensaje: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== FIN DE LA PRUEBA ===\n"; 