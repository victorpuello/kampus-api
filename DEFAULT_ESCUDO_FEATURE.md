# Funcionalidad de Imagen por Defecto para Escudos de Instituciones

## Descripción

Se ha implementado una funcionalidad que asigna automáticamente una imagen por defecto cuando una institución no tiene un escudo cargado o cuando el archivo del escudo no existe.

## Características

### 1. Imagen por Defecto
- **Ubicación**: `storage/app/public/instituciones/escudos/default.png`
- **Dimensiones**: 300x300 píxeles
- **Formato**: PNG
- **Diseño**: Imagen simple con texto "ESCUDO POR DEFECTO" en un fondo gris claro

### 2. Implementación Técnica

#### Modelo Institucion (`app/Models/Institucion.php`)
- **Mutador `setEscudoAttribute`**: Asigna automáticamente la imagen por defecto cuando el campo está vacío
- **Accesor `getEscudoAttribute`**: Retorna la imagen por defecto si el archivo no existe

#### Trait HasFileUploads (`app/Traits/HasFileUploads.php`)
- **Método `getFileUrl`**: Modificado para retornar la URL de la imagen por defecto cuando el campo escudo está vacío o el archivo no existe

### 3. Comportamiento

#### Escenarios Cubiertos:
1. **Institución sin escudo**: Se asigna automáticamente `instituciones/escudos/default.png`
2. **Escudo con archivo inexistente**: Se retorna la imagen por defecto
3. **Actualización con escudo vacío**: Se asigna la imagen por defecto
4. **Valor null**: Se asigna la imagen por defecto

#### Ejemplos de Uso:
```php
// Crear institución sin escudo
$institucion = Institucion::create([
    'nombre' => 'Mi Institución',
    'siglas' => 'MI'
]);
// $institucion->escudo será 'instituciones/escudos/default.png'

// Obtener URL del escudo
$url = $institucion->getFileUrl('escudo');
// Retorna: http://kampus.test/storage/instituciones/escudos/default.png
```

### 4. Pruebas Implementadas

#### Pruebas Unitarias (`tests/Feature/InstitucionControllerTest.php`)
- `test_uses_default_escudo_when_no_escudo_provided()`: Verifica que se asigne la imagen por defecto cuando no se proporciona escudo
- `test_uses_default_escudo_when_escudo_file_missing()`: Verifica que se use la imagen por defecto cuando el archivo no existe

### 5. Beneficios

1. **Experiencia de Usuario**: Las instituciones siempre tendrán una imagen visible, evitando espacios vacíos en la interfaz
2. **Consistencia**: Todas las instituciones tienen un escudo válido
3. **Robustez**: El sistema maneja graciosamente archivos faltantes o corruptos
4. **Mantenibilidad**: Fácil de personalizar cambiando la imagen por defecto

### 6. Personalización

Para cambiar la imagen por defecto:
1. Reemplazar el archivo `storage/app/public/instituciones/escudos/default.png`
2. Mantener las mismas dimensiones (300x300) para consistencia
3. Usar formato PNG para mejor compatibilidad

### 7. Consideraciones Técnicas

- La imagen por defecto se almacena en el storage público para acceso directo
- El enlace simbólico del storage debe estar configurado (`php artisan storage:link`)
- La funcionalidad es transparente para el frontend - no requiere cambios en la interfaz
- Compatible con el sistema de carga de archivos existente

## Archivos Modificados

1. `app/Models/Institucion.php` - Agregados mutador y accesor para el campo escudo
2. `app/Traits/HasFileUploads.php` - Modificado método getFileUrl para manejar imagen por defecto
3. `tests/Feature/InstitucionControllerTest.php` - Agregadas pruebas para la nueva funcionalidad
4. `storage/app/public/instituciones/escudos/default.png` - Imagen por defecto creada

## Estado de las Pruebas

✅ Todas las pruebas pasan correctamente (14/14)
✅ Funcionalidad verificada en diferentes escenarios
✅ Compatibilidad con el sistema existente confirmada 