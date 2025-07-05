# 📚 Estructura de Relaciones Corregida - Sistema Educativo

## 🎯 **Lógica Real de Relaciones**

### **Jerarquía Correcta:**
```
Institución (1)
    ↓ hasMany
Sede (N)
    ↓ hasMany
Grupo (N)
    ↓ belongsTo
Grado (1)
    ↓ belongsTo
Institución (1)

Estudiante (N)
    ↓ belongsTo
Grupo (1)
    ↓ (hereda grado, sede e institución a través del grupo)
```

## 🔗 **Relaciones Implementadas**

### **1. Institución → Sede**
- **Relación**: `hasMany`
- **Descripción**: Una institución puede tener múltiples sedes
- **Implementación**: 
  ```php
  // En Institucion.php
  public function sedes()
  {
      return $this->hasMany(Sede::class);
  }
  ```

### **2. Sede → Grupo**
- **Relación**: `hasMany`
- **Descripción**: Una sede puede tener múltiples grupos
- **Implementación**:
  ```php
  // En Sede.php
  public function grupos()
  {
      return $this->hasMany(Grupo::class);
  }
  ```

### **3. Grupo → Grado**
- **Relación**: `belongsTo`
- **Descripción**: Un grupo pertenece a un grado específico
- **Validación**: El grado debe pertenecer a la misma institución de la sede
- **Implementación**:
  ```php
  // En Grupo.php
  public function grado()
  {
      return $this->belongsTo(Grado::class);
  }
  ```

### **4. Estudiante → Grupo**
- **Relación**: `belongsTo`
- **Descripción**: Un estudiante pertenece a un grupo específico
- **Herencia**: El estudiante hereda grado, sede e institución a través del grupo
- **Implementación**:
  ```php
  // En Estudiante.php
  public function grupo()
  {
      return $this->belongsTo(Grupo::class);
  }
  ```

## 🏗️ **Herencia de Propiedades**

### **Estudiante hereda:**
1. **Grado** → `$estudiante->grado` o `$estudiante->grupo->grado`
2. **Sede** → `$estudiante->sede` o `$estudiante->grupo->sede`
3. **Institución** → `$estudiante->institucion` o `$estudiante->grupo->sede->institucion`

### **Grupo hereda:**
1. **Sede** → `$grupo->sede`
2. **Institución** → `$grupo->institucion` o `$grupo->sede->institucion`

## 📊 **Campos Agregados en Migraciones**

### **Tabla `grupos`:**
- ✅ `sede_id` (unsignedBigInteger, foreign key a sedes.id)

### **Tabla `estudiantes`:**
- ✅ `grupo_id` (unsignedBigInteger, nullable, foreign key a grupos.id)

## 🔧 **Validaciones Implementadas**

### **En Grupo:**
```php
static::creating(function ($grupo) {
    // Validar que el grado pertenezca a la misma institución de la sede
    if ($grupo->sede && $grupo->grado) {
        if ($grupo->sede->institucion_id !== $grupo->grado->institucion_id) {
            throw new \Exception('El grado debe pertenecer a la misma institución de la sede');
        }
    }
});
```

## 🎯 **Métodos Helper Implementados**

### **En Grupo:**
- `getNombreCompletoAttribute()` - "Sede - Grado - Nombre"
- `scopePorSede()` - Filtrar por sede
- `scopePorGrado()` - Filtrar por grado
- `scopePorAnio()` - Filtrar por año académico
- `scopePorInstitucion()` - Filtrar por institución

### **En Sede:**
- `gruposPorAnio()` - Grupos por año específico
- `gruposPorGrado()` - Grupos por grado específico
- `estadisticasGruposPorNivel()` - Estadísticas por nivel educativo
- `totalEstudiantesPorAnio()` - Total de estudiantes por año

### **En Estudiante:**
- `getUbicacionAcademicaAttribute()` - "Sede - Grado - Grupo"
- `scopePorGrupo()` - Filtrar por grupo
- `scopePorGrado()` - Filtrar por grado (a través del grupo)
- `scopePorSede()` - Filtrar por sede (a través del grupo)
- `scopePorInstitucion()` - Filtrar por institución (a través del grupo)
- `scopePorAnio()` - Filtrar por año académico (a través del grupo)

## 🏭 **Factories Actualizadas**

### **GrupoFactory:**
- ✅ Incluye `sede_id`
- ✅ Método `paraInstitucion()` - Asegura sede y grado de la misma institución
- ✅ Método `paraAnio()` - Configura año académico específico

### **EstudianteFactory:**
- ✅ Incluye `grupo_id` (opcional)
- ✅ Método `enGrupo()` - Asigna a grupo específico
- ✅ Método `enInstitucion()` - Asigna a grupo de institución específica

## 📋 **Ejemplos de Uso**

### **Obtener estudiantes de una institución:**
```php
$estudiantes = Estudiante::porInstitucion($institucionId)->get();
```

### **Obtener grupos de una sede por año:**
```php
$grupos = $sede->gruposPorAnio($anioId)->get();
```

### **Obtener ubicación académica de un estudiante:**
```php
$ubicacion = $estudiante->ubicacion_academica;
// Resultado: "Sede Principal - Grado 10º - Grupo A"
```

### **Obtener estadísticas de grupos por nivel:**
```php
$estadisticas = $sede->estadisticasGruposPorNivel($anioId);
// Resultado: ['Preescolar' => 2, 'Básica Primaria' => 5, ...]
```

## ✅ **Ventajas de la Nueva Estructura**

1. **Lógica Realista**: Refleja la estructura real de instituciones educativas
2. **Herencia Clara**: Los estudiantes heredan propiedades a través del grupo
3. **Validaciones**: Asegura consistencia de datos entre instituciones
4. **Flexibilidad**: Permite múltiples sedes por institución
5. **Escalabilidad**: Fácil de extender para más funcionalidades
6. **Consultas Eficientes**: Métodos helper para consultas comunes

## 🔄 **Migración de Datos**

Si existen datos previos, se recomienda:
1. Ejecutar las migraciones
2. Actualizar registros existentes con `sede_id` y `grupo_id` apropiados
3. Validar que las relaciones sean consistentes
4. Ejecutar pruebas para verificar la integridad de datos 