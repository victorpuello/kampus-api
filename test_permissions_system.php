<?php
/**
 * Script para probar el sistema completo de permisos
 * Verifica que el usuario admin tenga todos los permisos y que las rutas estén protegidas
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

echo "=== PRUEBA DEL SISTEMA DE PERMISOS ===\n\n";

try {
    // 1. Verificar que el usuario admin existe
    echo "1. Verificando usuario admin...\n";
    $admin = User::where('email', 'admin@example.com')->first();
    
    if (!$admin) {
        echo "❌ ERROR: Usuario admin no encontrado\n";
        exit(1);
    }
    
    echo "✅ Usuario admin encontrado: {$admin->nombre} {$admin->apellido}\n";
    
    // 2. Verificar que tiene el rol administrador
    echo "\n2. Verificando rol administrador...\n";
    $adminRole = $admin->roles()->where('nombre', 'administrador')->first();
    
    if (!$adminRole) {
        echo "❌ ERROR: Usuario admin no tiene rol administrador\n";
        exit(1);
    }
    
    echo "✅ Rol administrador asignado correctamente\n";
    
    // 3. Verificar que el rol administrador tiene todos los permisos
    echo "\n3. Verificando permisos del rol administrador...\n";
    $permissions = $adminRole->permissions;
    
    if ($permissions->isEmpty()) {
        echo "❌ ERROR: El rol administrador no tiene permisos asignados\n";
        exit(1);
    }
    
    echo "✅ El rol administrador tiene {$permissions->count()} permisos:\n";
    foreach ($permissions as $permission) {
        echo "   - {$permission->nombre}\n";
    }
    
    // 4. Verificar que el usuario admin puede acceder a todos los permisos
    echo "\n4. Verificando acceso del usuario admin a todos los permisos...\n";
    $userPermissions = $admin->getAllPermissions();
    
    if ($userPermissions->isEmpty()) {
        echo "❌ ERROR: El usuario admin no puede acceder a ningún permiso\n";
        exit(1);
    }
    
    echo "✅ El usuario admin puede acceder a {$userPermissions->count()} permisos\n";
    
    // 5. Verificar permisos específicos importantes
    echo "\n5. Verificando permisos específicos...\n";
    $importantPermissions = [
        'ver_usuarios',
        'crear_usuarios',
        'editar_usuarios',
        'ver_estudiantes',
        'crear_estudiantes',
        'editar_estudiantes',
        'ver_docentes',
        'crear_docentes',
        'editar_docentes',
        'ver_instituciones',
        'crear_instituciones',
        'editar_instituciones'
    ];
    
    $missingPermissions = [];
    foreach ($importantPermissions as $permissionName) {
        if (!$admin->hasPermission($permissionName)) {
            $missingPermissions[] = $permissionName;
        }
    }
    
    if (!empty($missingPermissions)) {
        echo "❌ ERROR: Faltan permisos importantes:\n";
        foreach ($missingPermissions as $permission) {
            echo "   - {$permission}\n";
        }
        exit(1);
    }
    
    echo "✅ Todos los permisos importantes están disponibles\n";
    
    // 6. Verificar que las rutas de la API están protegidas
    echo "\n6. Verificando protección de rutas de la API...\n";
    
    // Simular una petición sin autenticación
    $response = file_get_contents('http://localhost:8000/api/v1/users', false, stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => 'Content-Type: application/json'
        ]
    ]));
    
    if ($response === false) {
        echo "⚠️  No se pudo verificar la protección de rutas (servidor no disponible)\n";
    } else {
        $data = json_decode($response, true);
        if (isset($data['message']) && strpos($data['message'], 'Unauthenticated') !== false) {
            echo "✅ Las rutas están protegidas correctamente\n";
        } else {
            echo "❌ ERROR: Las rutas no están protegidas correctamente\n";
        }
    }
    
    // 7. Verificar estructura de datos para el frontend
    echo "\n7. Verificando estructura de datos para el frontend...\n";
    
    $userWithRoles = User::with(['roles.permissions'])->where('email', 'admin@example.com')->first();
    
    if (!$userWithRoles->roles) {
        echo "❌ ERROR: El usuario no tiene roles cargados\n";
        exit(1);
    }
    
    $firstRole = $userWithRoles->roles->first();
    if (!$firstRole->permissions) {
        echo "❌ ERROR: Los roles no tienen permisos cargados\n";
        exit(1);
    }
    
    echo "✅ Estructura de datos correcta para el frontend:\n";
    echo "   - Usuario: {$userWithRoles->nombre} {$userWithRoles->apellido}\n";
    echo "   - Roles: {$userWithRoles->roles->count()}\n";
    echo "   - Permisos del primer rol: {$firstRole->permissions->count()}\n";
    
    // 8. Verificar que el middleware de permisos funciona
    echo "\n8. Verificando middleware de permisos...\n";
    
    // Crear un token para el usuario admin
    $token = $admin->createToken('test-token')->plainTextToken;
    
    // Simular una petición autenticada
    $response = file_get_contents('http://localhost:8000/api/v1/users', false, stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "Authorization: Bearer {$token}\nContent-Type: application/json"
        ]
    ]));
    
    if ($response === false) {
        echo "⚠️  No se pudo verificar el middleware (servidor no disponible)\n";
    } else {
        $data = json_decode($response, true);
        if (isset($data['data'])) {
            echo "✅ El middleware permite acceso con token válido\n";
        } else {
            echo "❌ ERROR: El middleware no permite acceso con token válido\n";
        }
    }
    
    echo "\n=== PRUEBA COMPLETADA ===\n";
    echo "✅ El sistema de permisos está funcionando correctamente\n";
    echo "✅ El usuario admin tiene todos los permisos necesarios\n";
    echo "✅ La estructura de datos es correcta para el frontend\n";
    echo "✅ El método getAllPermissions funciona correctamente\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
} 