# Módulos de Áreas y Asignaturas

## Descripción General

Este documento describe la implementación de los módulos de **Áreas** y **Asignaturas** en el sistema académico Kampus. Ambos módulos están completamente integrados con el backend Laravel y siguen el sistema de diseño establecido.

## Módulo de Áreas

### Componentes

#### `AreaForm.tsx`
- **Ubicación**: `src/components/areas/AreaForm.tsx`
- **Propósito**: Formulario reutilizable para crear y editar áreas
- **Características**:
  - Campos: nombre, descripción, código, color, estado
  - Validación de campos requeridos
  - Selector de color con preview visual
  - Manejo de errores de validación
  - Estados de carga

#### `AreasListPage.tsx`
- **Ubicación**: `src/pages/AreasListPage.tsx`
- **Propósito**: Página principal para listar todas las áreas
- **Características**:
  - DataTable con búsqueda y ordenamiento
  - Paginación configurable
  - Acciones individuales (ver, editar, eliminar)
  - Acciones en lote (eliminar múltiples)
  - Visualización de estadísticas (asignaturas asociadas)
  - Navegación directa a detalles

#### `CreateAreaPage.tsx`
- **Ubicación**: `src/pages/CreateAreaPage.tsx`
- **Propósito**: Página para crear nuevas áreas
- **Características**:
  - Formulario con validación
  - Navegación automática después de crear
  - Manejo de errores del backend

#### `EditAreaPage.tsx`
- **Ubicación**: `src/pages/EditAreaPage.tsx`
- **Propósito**: Página para editar áreas existentes
- **Características**:
  - Carga automática de datos existentes
  - Formulario pre-poblado
  - Validación y actualización

#### `AreaDetailPage.tsx`
- **Ubicación**: `src/pages/AreaDetailPage.tsx`
- **Propósito**: Página de detalle completo de un área
- **Características**:
  - Información completa del área
  - Estadísticas (total de asignaturas, activas)
  - Lista de asignaturas asociadas
  - Navegación a asignaturas individuales
  - Acciones (editar, eliminar)
  - Confirmación de eliminación

### Rutas

```typescript
// Rutas del módulo de áreas
{
  path: 'areas',
  element: <DashboardLayout><AreasListPage /></DashboardLayout>
},
{
  path: 'areas/crear',
  element: <DashboardLayout><CreateAreaPage /></DashboardLayout>
},
{
  path: 'areas/:id',
  element: <DashboardLayout><AreaDetailPage /></DashboardLayout>
},
{
  path: 'areas/:id/editar',
  element: <DashboardLayout><EditAreaPage /></DashboardLayout>
}
```

### Características Técnicas

- **Interfaz TypeScript**: Definición completa de tipos para áreas
- **Validación**: Validación tanto en frontend como backend
- **Estados de carga**: Indicadores visuales durante operaciones
- **Manejo de errores**: Captura y visualización de errores
- **Navegación**: Navegación bidireccional entre módulos
- **Responsive**: Diseño adaptativo para móviles y desktop

## Módulo de Asignaturas

### Componentes

#### `AsignaturaForm.tsx`
- **Ubicación**: `src/components/asignaturas/AsignaturaForm.tsx`
- **Propósito**: Formulario reutilizable para crear y editar asignaturas
- **Características**:
  - Campos: nombre, código, descripción, créditos, área, estado, grados
  - Selector de área con carga dinámica
  - Multi-selector de grados con checkboxes
  - Validación de campos requeridos
  - Estados de carga para datos dependientes

#### `AsignaturasListPage.tsx`
- **Ubicación**: `src/pages/AsignaturasListPage.tsx`
- **Propósito**: Página principal para listar todas las asignaturas
- **Características**:
  - DataTable con búsqueda avanzada
  - Visualización de área asociada con color
  - Estadísticas de grados asociados
  - Acciones individuales y en lote
  - Navegación directa a detalles

#### `CreateAsignaturaPage.tsx`
- **Ubicación**: `src/pages/CreateAsignaturaPage.tsx`
- **Propósito**: Página para crear nuevas asignaturas
- **Características**:
  - Formulario completo con relaciones
  - Carga automática de áreas y grados
  - Validación y creación

#### `EditAsignaturaPage.tsx`
- **Ubicación**: `src/pages/EditAsignaturaPage.tsx`
- **Propósito**: Página para editar asignaturas existentes
- **Características**:
  - Carga de datos existentes
  - Formulario pre-poblado
  - Actualización de relaciones

#### `AsignaturaDetailPage.tsx`
- **Ubicación**: `src/pages/AsignaturaDetailPage.tsx`
- **Propósito**: Página de detalle completo de una asignatura
- **Características**:
  - Información completa de la asignatura
  - Visualización del área asociada
  - Lista de grados donde se imparte
  - Navegación a áreas y grados
  - Acciones completas

### Rutas

```typescript
// Rutas del módulo de asignaturas
{
  path: 'asignaturas',
  element: <DashboardLayout><AsignaturasListPage /></DashboardLayout>
},
{
  path: 'asignaturas/crear',
  element: <DashboardLayout><CreateAsignaturaPage /></DashboardLayout>
},
{
  path: 'asignaturas/:id',
  element: <DashboardLayout><AsignaturaDetailPage /></DashboardLayout>
},
{
  path: 'asignaturas/:id/editar',
  element: <DashboardLayout><EditAsignaturaPage /></DashboardLayout>
}
```

### Características Técnicas

- **Relaciones complejas**: Gestión de relaciones área-asignatura-grado
- **Carga dinámica**: Carga de datos dependientes (áreas, grados)
- **Multi-selección**: Gestión de grados múltiples por asignatura
- **Validación cruzada**: Validación de relaciones entre entidades
- **Navegación bidireccional**: Navegación entre módulos relacionados

## Navegación y Menú

### Enlaces en el Menú Principal

```typescript
// Enlaces agregados al menú de navegación
{
  path: '/areas',
  label: 'Áreas',
  icon: <AreaIcon />
},
{
  path: '/asignaturas',
  label: 'Asignaturas',
  icon: <AsignaturaIcon />
}
```

### Navegación entre Módulos

- **Áreas → Asignaturas**: Desde el detalle de área se puede navegar a asignaturas asociadas
- **Asignaturas → Áreas**: Desde el detalle de asignatura se puede navegar al área asociada
- **Asignaturas → Grados**: Desde el detalle de asignatura se puede navegar a grados asociados
- **Grados → Asignaturas**: Desde el detalle de grado se puede navegar a asignaturas asociadas

## Integración con Backend

### Endpoints Utilizados

#### Áreas
- `GET /areas` - Listar todas las áreas
- `GET /areas/{id}` - Obtener área específica
- `POST /areas` - Crear nueva área
- `PUT /areas/{id}` - Actualizar área
- `DELETE /areas/{id}` - Eliminar área

#### Asignaturas
- `GET /asignaturas` - Listar todas las asignaturas
- `GET /asignaturas/{id}` - Obtener asignatura específica
- `POST /asignaturas` - Crear nueva asignatura
- `PUT /asignaturas/{id}` - Actualizar asignatura
- `DELETE /asignaturas/{id}` - Eliminar asignatura

### Estructura de Datos

#### Área
```typescript
interface Area {
  id: number;
  nombre: string;
  descripcion?: string;
  codigo?: string;
  color?: string;
  estado: string;
  asignaturas_count?: number;
  asignaturas?: Array<{
    id: number;
    nombre: string;
    codigo?: string;
    creditos?: number;
    estado: string;
  }>;
}
```

#### Asignatura
```typescript
interface Asignatura {
  id: number;
  nombre: string;
  codigo?: string;
  descripcion?: string;
  creditos?: number;
  estado: string;
  area_id: number;
  area?: {
    id: number;
    nombre: string;
    codigo?: string;
    color?: string;
  };
  grados?: Array<{
    id: number;
    nombre: string;
    nivel?: string;
  }>;
  grados_count?: number;
}
```

## Características Destacadas

### Gestión de Relaciones
- **Área-Asignatura**: Relación uno a muchos
- **Asignatura-Grado**: Relación muchos a muchos
- **Navegación bidireccional**: Acceso desde cualquier entidad a sus relaciones

### Interfaz de Usuario
- **Diseño consistente**: Sigue el sistema de diseño establecido
- **Responsive**: Adaptable a diferentes tamaños de pantalla
- **Accesibilidad**: Navegación por teclado y lectores de pantalla
- **Feedback visual**: Estados de carga, éxito y error

### Funcionalidades Avanzadas
- **Búsqueda y filtrado**: Búsqueda por múltiples campos
- **Ordenamiento**: Ordenamiento por cualquier columna
- **Paginación**: Paginación configurable
- **Acciones en lote**: Operaciones sobre múltiples elementos
- **Confirmaciones**: Diálogos de confirmación para acciones destructivas

## Estado del Proyecto

✅ **Completado**:
- Formularios de creación y edición
- Páginas de lista con DataTable
- Páginas de detalle con relaciones
- Navegación y rutas
- Integración con backend
- Validación y manejo de errores
- Documentación completa

🎯 **Listo para Producción**:
- Ambos módulos están completamente funcionales
- Integrados con el sistema existente
- Siguen las mejores prácticas establecidas
- Documentación completa disponible

## Próximos Pasos

1. **Testing**: Implementar pruebas unitarias y de integración
2. **Optimización**: Optimizar consultas y carga de datos
3. **Funcionalidades adicionales**: Exportación, importación masiva
4. **Reportes**: Generación de reportes específicos por área/asignatura 