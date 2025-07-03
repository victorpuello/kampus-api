<?php

require_once 'vendor/autoload.php';

use App\Models\Institucion;
use App\Services\FileUploadService;
use Illuminate\Http\UploadedFile;

// Simular entorno Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PRUEBA DE CARGA DE ARCHIVOS ===\n\n";

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
    $testImageContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
    file_put_contents($testImagePath, $testImageContent);
    
    echo "Archivo de prueba creado: " . (file_exists($testImagePath) ? '✅' : '❌') . "\n";
    
    // 6. Simular UploadedFile
    echo "\n6️⃣ Simulando UploadedFile...\n";
    
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
    
    // 7. Probar carga de imagen
    echo "\n7️⃣ Probando carga de imagen...\n";
    
    try {
        $result = $fileService->uploadImage($uploadedFile, 'instituciones/escudos', [
            'resize' => true,
            'width' => 300,
            'height' => 300,
            'quality' => 85
        ]);
        
        echo "Carga exitosa: ✅\n";
        echo "Archivo guardado en: " . $result['path'] . "\n";
        echo "URL: " . $result['url'] . "\n";
        echo "Tamaño: " . $result['size'] . " bytes\n";
        
        // Verificar que el archivo existe
        $fullPath = storage_path('app/public/' . $result['path']);
        echo "Archivo existe en disco: " . (file_exists($fullPath) ? '✅' : '❌') . "\n";
        
    } catch (Exception $e) {
        echo "Error en carga: ❌\n";
        echo "Mensaje: " . $e->getMessage() . "\n";
        echo "Archivo: " . $e->getFile() . "\n";
        echo "Línea: " . $e->getLine() . "\n";
    }
    
    // 8. Probar con modelo Institucion
    echo "\n8️⃣ Probando con modelo Institucion...\n";
    
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