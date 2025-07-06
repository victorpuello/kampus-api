# Correcciones Necesarias para Tests de Feature

## Resumen de Problemas Encontrados

### 1. Problemas de Autenticación y Permisos (403 errors)

**Tests afectados:**
- `GradoControllerTest`
- `GrupoControllerTest`
- `RoleControllerTest` (algunos métodos)

**Causa:** Los tests no están configurando correctamente los permisos necesarios para acceder a los endpoints.

**Solución:** Agregar permisos específicos al usuario de prueba en el setUp de cada test.

### 2. Problemas con Campos Requeridos

**Tests afectados:**
- `AulaControllerTest` - Campo `tipo` requerido
- `FranjaHorariaControllerTest` - Campo `nombre` requerido

**Causa:** Los factories no están proporcionando todos los campos requeridos por las migraciones.

**Solución:** Actualizar los factories para incluir todos los campos requeridos.

### 3. Problemas con Sanctum Tokens

**Tests afectados:**
- `AuthControllerTest`
- `AuthTest`
- `AuthenticationMiddlewareTest`

**Causa:** Cambios en la API de Sanctum y comportamiento de logout.

**Solución:** Actualizar los tests para reflejar el comportamiento actual de Sanctum.

### 4. Problemas con Rutas que No Existen (405 errors)

**Tests afectados:**
- `RoleControllerTest` - Endpoints de roles no implementados

**Causa:** Los endpoints de roles no están implementados en el controlador.

**Solución:** Implementar los endpoints faltantes o actualizar los tests para reflejar la implementación actual.

### 5. Problemas con Validaciones

**Tests afectados:**
- `SedeControllerTest` - Validación de teléfono
- `GradoIntegrityTest` - Validación de nivel

**Causa:** Las validaciones han cambiado o no están implementadas como esperan los tests.

**Solución:** Actualizar las validaciones o ajustar los tests.

## Correcciones Específicas por Test

### AulaControllerTest
```php
// En el setUp, agregar el campo tipo
$aulaData = [
    'nombre' => 'Nueva Aula',
    'tipo' => 'Salón', // Agregar este campo
    'capacidad' => 30,
    'institucion_id' => $this->institucion->id
];
```

### FranjaHorariaControllerTest
```php
// En el setUp, agregar el campo nombre
$franjaData = [
    'nombre' => 'Franja Test', // Agregar este campo
    'hora_inicio' => '08:00:00',
    'hora_fin' => '09:00:00',
    'institucion_id' => $this->institucion->id
];
```

### GradoControllerTest
```php
// En el setUp, agregar permisos específicos
protected function setUp(): void
{
    parent::setUp();
    
    // Crear permisos específicos para grados
    $verGrados = Permission::factory()->create(['nombre' => 'ver_grados']);
    $crearGrados = Permission::factory()->create(['nombre' => 'crear_grados']);
    $editarGrados = Permission::factory()->create(['nombre' => 'editar_grados']);
    $eliminarGrados = Permission::factory()->create(['nombre' => 'eliminar_grados']);
    
    // Asignar permisos al rol del usuario
    $this->adminRole->permissions()->attach([
        $verGrados->id,
        $crearGrados->id,
        $editarGrados->id,
        $eliminarGrados->id
    ]);
}
```

### RoleControllerTest
```php
// Los endpoints de roles no están implementados, actualizar tests para reflejar esto
// o implementar los endpoints faltantes en RoleController
```

### AuthenticationMiddlewareTest
```php
// Actualizar para usar la API correcta de Sanctum
$token = $this->user->createToken('test-token');
$token->accessToken->delete(); // Usar accessToken->delete() en lugar de token->delete()
```

## Recomendaciones

1. **Revisar implementación actual:** Verificar qué endpoints están realmente implementados
2. **Actualizar factories:** Asegurar que todos los campos requeridos estén incluidos
3. **Configurar permisos:** Agregar permisos específicos en el setUp de cada test
4. **Actualizar validaciones:** Ajustar tests para reflejar las validaciones actuales
5. **Revisar rutas:** Verificar que las rutas esperadas existan y funcionen

## Estado Actual

- **Tests Unit:** ✅ Todos pasando (152/152)
- **Tests Feature:** ❌ 70 fallando, 205 pasando

## Próximos Pasos

1. Implementar correcciones por prioridad
2. Revisar implementación de endpoints faltantes
3. Actualizar documentación de API
4. Ejecutar tests nuevamente después de cada corrección 