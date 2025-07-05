<?php

require_once 'vendor/autoload.php';

use App\Models\Institucion;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

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

// Configurar la URL base
$baseUrl = 'http://kampus.test/api/v1';

// Datos de prueba (simulando el formulario del frontend)
$institutionId = 1;
$updateData = [
    'nombre' => 'Institución Frontend Test',
    'siglas' => 'IFT',
    'slogan' => '', // Campo vacío para probar que se actualiza
    'dane' => '',
    'resolucion_aprobacion' => '',
    'direccion' => 'Dirección de prueba',
    'telefono' => '',
    'email' => 'test@frontend.edu.co',
    'rector' => 'Rector de Prueba'
];

echo "🔍 Probando actualización como frontend...\n";
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
        echo "   Slogan: " . ($currentData['slogan'] ?? 'N/A') . "\n";
        echo "   Email: " . ($currentData['email'] ?? 'N/A') . "\n\n";
    } else {
        echo "❌ Error al obtener institución: " . $response->status() . "\n";
        echo $response->body() . "\n";
        exit(1);
    }

    // 2. Simular FormData como lo hace el frontend
    echo "🔄 Simulando FormData del frontend...\n";
    
    // Crear un archivo temporal para simular FormData
    $tempFile = tempnam(sys_get_temp_dir(), 'test_');
    file_put_contents($tempFile, 'test image content');
    
    // Simular FormData con campos vacíos incluidos
    $formData = [];
    foreach ($updateData as $key => $value) {
        $formData[$key] = $value !== null ? $value : '';
    }
    
    echo "📤 Datos a enviar:\n";
    foreach ($formData as $key => $value) {
        echo "   $key: '$value'\n";
    }
    echo "\n";

    // 3. Actualizar la institución usando multipart/form-data
    echo "🔄 Actualizando institución con FormData...\n";
    $updateResponse = Http::attach('escudo', file_get_contents($tempFile), 'test.jpg', ['Content-Type' => 'image/jpeg'])
        ->put("$baseUrl/instituciones/$institutionId", $formData);
    
    if ($updateResponse->successful()) {
        $updatedData = $updateResponse->json('data');
        echo "✅ Institución actualizada exitosamente:\n";
        echo "   Nombre: " . $updatedData['nombre'] . "\n";
        echo "   Siglas: " . $updatedData['siglas'] . "\n";
        echo "   Slogan: " . ($updatedData['slogan'] ?? 'N/A') . "\n";
        echo "   Email: " . ($updatedData['email'] ?? 'N/A') . "\n";
        echo "   Escudo: " . ($updatedData['escudo'] ?? 'N/A') . "\n\n";
    } else {
        echo "❌ Error al actualizar institución: " . $updateResponse->status() . "\n";
        echo $updateResponse->body() . "\n";
        exit(1);
    }

    // 4. Verificar que los cambios se guardaron
    echo "🔍 Verificando cambios...\n";
    $verifyResponse = Http::get("$baseUrl/instituciones/$institutionId");
    
    if ($verifyResponse->successful()) {
        $verifiedData = $verifyResponse->json('data');
        echo "✅ Verificación completada:\n";
        echo "   Nombre: " . $verifiedData['nombre'] . "\n";
        echo "   Siglas: " . $verifiedData['siglas'] . "\n";
        echo "   Slogan: " . ($verifiedData['slogan'] ?? 'N/A') . "\n";
        echo "   Email: " . ($verifiedData['email'] ?? 'N/A') . "\n";
        echo "   Escudo: " . ($verifiedData['escudo'] ?? 'N/A') . "\n\n";
        
        // Verificar que los cambios se aplicaron
        $changesApplied = true;
        foreach ($updateData as $field => $value) {
            $actualValue = $verifiedData[$field] ?? null;
            if ($actualValue !== $value) {
                echo "❌ Campo '$field' no se actualizó correctamente\n";
                echo "   Esperado: '$value'\n";
                echo "   Actual: '$actualValue'\n";
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

    // Limpiar archivo temporal
    unlink($tempFile);

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
} 