<?php

// Primero hacer login para obtener el token
$loginUrl = 'http://kampus.test/api/v1/login';
$loginData = [
    'email' => 'admin@example.com',
    'password' => '123456'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $loginUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo "❌ Error en login: $httpCode\n";
    echo "Respuesta: $response\n";
    exit(1);
}

$loginResponse = json_decode($response, true);
$token = $loginResponse['token'];

echo "✅ Login exitoso\n";
echo "Token: " . substr($token, 0, 20) . "...\n\n";

// Obtener la lista de áreas
echo "📋 Obteniendo lista de áreas...\n";
$areasUrl = 'http://kampus.test/api/v1/areas';

$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, $areasUrl);
curl_setopt($ch2, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json'
]);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);

$response2 = curl_exec($ch2);
$httpCode2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
curl_close($ch2);

if ($httpCode2 !== 200) {
    echo "❌ Error al obtener áreas: $httpCode2\n";
    echo "Respuesta: $response2\n";
    exit(1);
}

$areasResponse = json_decode($response2, true);
$areas = $areasResponse['data'] ?? [];

echo "✅ Áreas obtenidas: " . count($areas) . "\n\n";

if (empty($areas)) {
    echo "❌ No hay áreas disponibles\n";
    exit(1);
}

// Obtener el detalle de la primera área
$firstArea = $areas[0];
$areaId = $firstArea['id'];
$areaName = $firstArea['nombre'];

echo "🔍 Obteniendo detalle del área: $areaName (ID: $areaId)\n";
$areaDetailUrl = "http://kampus.test/api/v1/areas/$areaId";

$ch3 = curl_init();
curl_setopt($ch3, CURLOPT_URL, $areaDetailUrl);
curl_setopt($ch3, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json'
]);
curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch3, CURLOPT_SSL_VERIFYPEER, false);

$response3 = curl_exec($ch3);
$httpCode3 = curl_getinfo($ch3, CURLINFO_HTTP_CODE);
curl_close($ch3);

echo "Código HTTP: $httpCode3\n";

if ($httpCode3 !== 200) {
    echo "❌ Error al obtener detalle del área: $httpCode3\n";
    echo "Respuesta: $response3\n";
    exit(1);
}

$areaDetail = json_decode($response3, true);
$areaData = $areaDetail['data'] ?? $areaDetail;

echo "✅ Detalle del área obtenido\n";
echo "Nombre: " . $areaData['nombre'] . "\n";
echo "Descripción: " . ($areaData['descripcion'] ?? 'Sin descripción') . "\n";
echo "Código: " . ($areaData['codigo'] ?? 'Sin código') . "\n";
echo "Color: " . ($areaData['color'] ?? 'Sin color') . "\n";
echo "Estado: " . $areaData['estado'] . "\n";

if (isset($areaData['asignaturas'])) {
    $asignaturas = $areaData['asignaturas'];
    echo "📚 Asignaturas asociadas: " . count($asignaturas) . "\n";
    
    if (!empty($asignaturas)) {
        foreach ($asignaturas as $asignatura) {
            echo "  - " . $asignatura['nombre'];
            if (isset($asignatura['codigo'])) {
                echo " (" . $asignatura['codigo'] . ")";
            }
            echo " - " . $asignatura['estado'];
            if (isset($asignatura['porcentaje_area'])) {
                echo " - " . $asignatura['porcentaje_area'] . "%";
            }
            echo "\n";
        }
    } else {
        echo "  No hay asignaturas asociadas\n";
    }
} else {
    echo "❌ No se encontró el campo 'asignaturas' en la respuesta\n";
    echo "Respuesta completa: " . json_encode($areaData, JSON_PRETTY_PRINT) . "\n";
} 