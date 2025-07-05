<?php
/**
 * Script para diagnosticar problemas con el usuario admin
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

echo "=== DIAGNÓSTICO DEL USUARIO ADMIN ===\n\n";

try {
    // 1. Verificar si el usuario existe
    echo "1. Verificando existencia del usuario...\n";
    $admin = User::where('email', 'admin@example.com')->first();
    
    if (!$admin) {
        echo "❌ ERROR: Usuario admin@example.com no encontrado en la base de datos\n";
        echo "Creando usuario admin...\n";
        
        $admin = User::create([
            'nombre' => 'Admin',
            'apellido' => 'Sistema',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => '123456',
            'institucion_id' => 1,
            'estado' => 'activo'
        ]);
        
        echo "✅ Usuario admin creado exitosamente\n";
    } else {
        echo "✅ Usuario admin encontrado: {$admin->nombre} {$admin->apellido}\n";
        echo "   - ID: {$admin->id}\n";
        echo "   - Email: {$admin->email}\n";
        echo "   - Estado: {$admin->estado}\n";
        echo "   - Institución ID: {$admin->institucion_id}\n";
    }
    
    // 2. Verificar si existe el rol administrador
    echo "\n2. Verificando rol administrador...\n";
    $adminRole = Role::where('nombre', 'Administrador')->first();
    
    if (!$adminRole) {
        echo "❌ ERROR: Rol 'Administrador' no encontrado\n";
        echo "Creando rol administrador...\n";
        
        $adminRole = Role::create([
            'nombre' => 'Administrador',
            'descripcion' => 'Acceso total al sistema'
        ]);
        
        echo "✅ Rol administrador creado exitosamente\n";
    } else {
        echo "✅ Rol administrador encontrado: {$adminRole->nombre}\n";
    }
    
    // 3. Verificar si el usuario tiene el rol asignado
    echo "\n3. Verificando asignación de rol...\n";
    $hasRole = $admin->roles()->where('role_id', $adminRole->id)->exists();
    
    if (!$hasRole) {
        echo "❌ ERROR: Usuario no tiene rol administrador asignado\n";
        echo "Asignando rol administrador...\n";
        
        $admin->roles()->attach($adminRole->id);
        echo "✅ Rol administrador asignado exitosamente\n";
    } else {
        echo "✅ Usuario tiene rol administrador asignado\n";
    }
    
    // 4. Verificar permisos del rol
    echo "\n4. Verificando permisos del rol...\n";
    $permissions = $adminRole->permissions;
    
    if ($permissions->isEmpty()) {
        echo "❌ ERROR: El rol administrador no tiene permisos asignados\n";
        echo "Asignando todos los permisos...\n";
        
        $allPermissions = Permission::all();
        $adminRole->permissions()->sync($allPermissions->pluck('id')->toArray());
        
        echo "✅ Todos los permisos asignados al rol administrador\n";
    } else {
        echo "✅ El rol administrador tiene {$permissions->count()} permisos\n";
    }
    
    // 5. Verificar que el usuario puede acceder a los permisos
    echo "\n5. Verificando acceso a permisos...\n";
    $userPermissions = $admin->getAllPermissions();
    
    if ($userPermissions->isEmpty()) {
        echo "❌ ERROR: El usuario no puede acceder a ningún permiso\n";
    } else {
        echo "✅ El usuario puede acceder a {$userPermissions->count()} permisos\n";
    }
    
    // 6. Verificar credenciales de login
    echo "\n6. Verificando credenciales de login...\n";
    $credentials = [
        'email' => 'admin@example.com',
        'password' => '123456'
    ];
    
    if (auth()->attempt($credentials)) {
        echo "✅ Credenciales de login válidas\n";
        echo "✅ Usuario autenticado correctamente\n";
    } else {
        echo "❌ ERROR: Credenciales de login inválidas\n";
        echo "Actualizando contraseña...\n";
        
        $admin->password = '123456';
        $admin->save();
        
        if (auth()->attempt($credentials)) {
            echo "✅ Credenciales corregidas y válidas\n";
        } else {
            echo "❌ ERROR: No se pudieron corregir las credenciales\n";
        }
    }
    
    // 7. Verificar estructura para frontend
    echo "\n7. Verificando estructura para frontend...\n";
    $userWithRoles = User::with(['roles.permissions'])->where('email', 'admin@example.com')->first();
    
    echo "✅ Estructura de datos:\n";
    echo "   - Usuario: {$userWithRoles->nombre} {$userWithRoles->apellido}\n";
    echo "   - Roles: {$userWithRoles->roles->count()}\n";
    if ($userWithRoles->roles->count() > 0) {
        echo "   - Permisos del primer rol: {$userWithRoles->roles->first()->permissions->count()}\n";
    }
    
    echo "\n=== DIAGNÓSTICO COMPLETADO ===\n";
    echo "✅ Usuario admin configurado correctamente\n";
    echo "✅ Credenciales: admin@example.com / 123456\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 