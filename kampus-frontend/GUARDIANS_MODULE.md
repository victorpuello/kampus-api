# Módulo de Acudientes - Kampus

Este documento describe la implementación completa del módulo de acudientes en el sistema Kampus, siguiendo el sistema de diseño establecido.

## 🎯 Características del Módulo

### ✅ Funcionalidades Implementadas

- **Lista de Acudientes**: Vista completa con búsqueda, ordenamiento y paginación
- **Crear Acudiente**: Formulario completo con validación
- **Editar Acudiente**: Modificación de datos existentes
- **Ver Detalle**: Información detallada del acudiente
- **Eliminar Acudiente**: Eliminación individual y en lote
- **Búsqueda Avanzada**: Filtrado por múltiples campos
- **Acciones en Lote**: Operaciones sobre múltiples acudientes seleccionados

### 🎨 Consistencia Visual

- **Sistema de Diseño**: Utiliza los componentes reutilizables establecidos
- **Responsive Design**: Optimizado para móviles, tablets y desktop
- **Estados de UI**: Loading, error, success y empty states
- **Accesibilidad**: Navegación por teclado y lectores de pantalla

## 📁 Estructura de Archivos

```
src/
├── components/
│   └── guardians/
│       └── GuardianForm.tsx          # Formulario reutilizable
├── pages/
│   ├── GuardiansListPage.tsx         # Lista principal
│   ├── CreateGuardianPage.tsx        # Crear acudiente
│   ├── EditGuardianPage.tsx          # Editar acudiente
│   └── GuardianDetailPage.tsx        # Ver detalle
└── router/
    └── index.tsx                     # Rutas del módulo
```

## 🧩 Componentes Creados

### GuardianForm
**Archivo**: `src/components/guardians/GuardianForm.tsx`

Formulario reutilizable que maneja:
- Creación y edición de acudientes
- Validación de campos requeridos
- Integración con el sistema de alertas
- Navegación automática después de operaciones

**Campos incluidos**:
- Nombre y apellido
- Tipo y número de documento
- Email y teléfono
- Dirección
- Parentesco (padre, madre, abuelo, etc.)
- Ocupación
- Estado (activo/inactivo)
- Institución
- Fecha de nacimiento
- Género

### Páginas del Módulo

#### GuardiansListPage
**Archivo**: `src/pages/GuardiansListPage.tsx`

Características:
- DataTable con búsqueda y ordenamiento
- Acciones individuales (ver, editar, eliminar)
- Acciones en lote (eliminar seleccionados)
- Estados de loading y error
- Navegación a crear nuevo acudiente

#### CreateGuardianPage
**Archivo**: `src/pages/CreateGuardianPage.tsx`

Características:
- PageHeader con título y descripción
- Card container para el formulario
- Integración con GuardianForm

#### EditGuardianPage
**Archivo**: `src/pages/EditGuardianPage.tsx`

Características:
- Carga automática de datos existentes
- PageHeader específico para edición
- Integración con GuardianForm en modo edición

#### GuardianDetailPage
**Archivo**: `src/pages/GuardianDetailPage.tsx`

Características:
- Vista detallada en cards organizadas
- Información personal y académica
- Lista de estudiantes asociados
- Botones de acción (editar, eliminar)
- Estados de loading y error

## 🛣️ Rutas Configuradas

```typescript
// Rutas del módulo de acudientes
{
  path: 'acudientes',
  element: <DashboardLayout><GuardiansListPage /></DashboardLayout>
},
{
  path: 'acudientes/crear',
  element: <DashboardLayout><CreateGuardianPage /></DashboardLayout>
},
{
  path: 'acudientes/:id',
  element: <DashboardLayout><GuardianDetailPage /></DashboardLayout>
},
{
  path: 'acudientes/:id/editar',
  element: <DashboardLayout><EditGuardianPage /></DashboardLayout>
}
```

## 🎨 Sistema de Diseño Aplicado

### Componentes Utilizados

1. **FormContainer**: Contenedor principal del formulario
2. **FormField**: Campos de entrada de texto
3. **FormSelect**: Campos de selección
4. **FormActions**: Botones de acción del formulario
5. **PageHeader**: Encabezados de página
6. **DataTable**: Tabla de datos con funcionalidades avanzadas
7. **Card**: Contenedores de información
8. **Button**: Botones con variantes
9. **Badge**: Etiquetas de estado
10. **ConfirmDialog**: Diálogos de confirmación

### Patrones de Diseño

- **Grid Responsivo**: `grid-cols-1 sm:grid-cols-2` para formularios
- **Espaciado Consistente**: `space-y-6` entre secciones
- **Estados Visuales**: Loading, error, success
- **Navegación Intuitiva**: Breadcrumbs y botones de acción

## 🔧 Integración con Backend

### Endpoints Utilizados

- `GET /acudientes` - Listar acudientes
- `POST /acudientes` - Crear acudiente
- `GET /acudientes/{id}` - Obtener acudiente específico
- `PUT /acudientes/{id}` - Actualizar acudiente
- `DELETE /acudientes/{id}` - Eliminar acudiente
- `GET /instituciones` - Listar instituciones (para select)

### Manejo de Errores

- Interceptores de axios para errores globales
- Manejo específico de errores por operación
- Alertas contextuales con el sistema de alertas
- Estados de loading para mejor UX

## 📱 Responsive Design

### Breakpoints Implementados

- **Mobile (< 640px)**: Una columna, botones apilados
- **Tablet (640px - 1024px)**: Dos columnas, layout adaptativo
- **Desktop (> 1024px)**: Layout completo, sidebar visible

### Adaptaciones Específicas

- Formularios: Grid de una columna en móvil, dos en desktop
- DataTable: Scroll horizontal en móvil
- Cards: Layout de una columna en móvil, dos en desktop
- Botones: Apilados en móvil, lado a lado en desktop

## 🎯 Beneficios del Módulo

### Para el Usuario
- **Interfaz Intuitiva**: Navegación clara y consistente
- **Operaciones Rápidas**: Búsqueda y filtrado eficiente
- **Feedback Visual**: Estados claros de todas las operaciones
- **Accesibilidad**: Navegación por teclado y lectores de pantalla

### Para el Desarrollo
- **Código Reutilizable**: Componentes modulares
- **Mantenibilidad**: Estructura clara y documentada
- **Escalabilidad**: Fácil agregar nuevas funcionalidades
- **Consistencia**: Mismo patrón en todo el módulo

## 🚀 Próximos Pasos

### Mejoras Futuras
- [ ] Exportación de datos (PDF, Excel)
- [ ] Filtros avanzados por institución, estado, etc.
- [ ] Historial de cambios
- [ ] Notificaciones automáticas
- [ ] Integración con módulo de estudiantes

### Optimizaciones
- [ ] Lazy loading de componentes
- [ ] Caché de datos
- [ ] Optimización de consultas
- [ ] Compresión de imágenes

## 📋 Checklist de Implementación

- [x] Formulario de acudientes con validación
- [x] Lista con búsqueda y ordenamiento
- [x] Páginas de crear, editar y ver detalle
- [x] Eliminación individual y en lote
- [x] Integración con sistema de alertas
- [x] Responsive design
- [x] Navegación y rutas
- [x] Integración con backend
- [x] Manejo de errores
- [x] Documentación completa

## 🎉 Resultado Final

El módulo de acudientes está completamente implementado y listo para uso en producción. Sigue todos los estándares del sistema de diseño establecido y proporciona una experiencia de usuario consistente con el resto de la aplicación.

**¡El módulo está funcional y listo para ser utilizado!** 