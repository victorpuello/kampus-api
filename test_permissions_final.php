<?php
/**
 * Script para probar el sistema completo de permisos
 * Verifica que el usuario admin tenga todos los permisos
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

echo "=== PRUEBA FINAL DEL SISTEMA DE PERMISOS ===\n\n";

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
    $adminRole = $admin->roles()->where('nombre', 'Administrador')->first();
    
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
        echo "   - {$permission->nombre}: {$permission->descripcion}\n";
    }
    
    // 4. Verificar que el método getAllPermissions funciona
    echo "\n4. Verificando método getAllPermissions...\n";
    $userPermissions = $admin->getAllPermissions();
    
    if ($userPermissions->isEmpty()) {
        echo "❌ ERROR: El método getAllPermissions no devuelve permisos\n";
        exit(1);
    }
    
    echo "✅ El método getAllPermissions devuelve {$userPermissions->count()} permisos únicos\n";
    
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
    
    // 6. Verificar estructura de datos para el frontend
    echo "\n6. Verificando estructura de datos para el frontend...\n";
    
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
    
    // 7. Verificar que el método hasPermission funciona
    echo "\n7. Verificando método hasPermission...\n";
    
    $testPermissions = ['ver_usuarios', 'crear_estudiantes', 'editar_docentes'];
    foreach ($testPermissions as $permission) {
        if ($admin->hasPermission($permission)) {
            echo "✅ El usuario tiene permiso: {$permission}\n";
        } else {
            echo "❌ ERROR: El usuario no tiene permiso: {$permission}\n";
            exit(1);
        }
    }
    
    echo "\n=== PRUEBA COMPLETADA EXITOSAMENTE ===\n";
    echo "✅ El sistema de permisos está funcionando correctamente\n";
    echo "✅ El usuario admin tiene todos los permisos necesarios\n";
    echo "✅ El método getAllPermissions funciona correctamente\n";
    echo "✅ El método hasPermission funciona correctamente\n";
    echo "✅ La estructura de datos es correcta para el frontend\n";
    echo "✅ Todos los permisos de instituciones están disponibles\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
} 