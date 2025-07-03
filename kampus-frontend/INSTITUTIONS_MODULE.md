# Módulo de Instituciones

## Descripción General

El módulo de Instituciones permite gestionar las instituciones educativas del sistema. Cada institución representa una entidad educativa que puede tener múltiples usuarios, grados, áreas y otros recursos asociados.

## Características Principales

### ✅ Funcionalidades Implementadas

- **Gestión CRUD completa** de instituciones
- **Formularios validados** con campos requeridos
- **Búsqueda y filtrado** de instituciones
- **Paginación** de resultados
- **Interfaz responsiva** para móviles y escritorio
- **Confirmaciones** para acciones destructivas
- **Mensajes de feedback** para el usuario

### 🎯 Campos del Modelo

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `id` | Integer | Sí | Identificador único |
| `nombre` | String | Sí | Nombre completo de la institución |
| `siglas` | String | Sí | Siglas o abreviatura (máx. 10 caracteres) |
| `created_at` | Timestamp | Sí | Fecha de creación |
| `updated_at` | Timestamp | Sí | Fecha de última actualización |

## Estructura de Archivos

```
kampus-frontend/src/
├── components/
│   └── institutions/
│       └── InstitutionForm.tsx          # Formulario reutilizable
├── pages/
│   ├── InstitutionsListPage.tsx         # Lista de instituciones
│   ├── CreateInstitutionPage.tsx        # Crear institución
│   ├── EditInstitutionPage.tsx          # Editar institución
│   └── InstitutionDetailPage.tsx        # Detalle de institución
└── router/
    └── index.tsx                        # Rutas del módulo
```

## Componentes

### InstitutionForm

Formulario reutilizable para crear y editar instituciones.

**Props:**
- `initialData?: InstitutionFormData` - Datos iniciales para edición
- `onSubmit: (data: InstitutionFormData) => void` - Función de envío
- `onCancel: () => void` - Función de cancelación
- `isLoading?: boolean` - Estado de carga
- `isEditing?: boolean` - Modo edición

**Validaciones:**
- Nombre: mínimo 3 caracteres, requerido
- Siglas: mínimo 2 caracteres, máximo 10, requerido

### InstitutionsListPage

Página principal que muestra la lista de instituciones con funcionalidades de búsqueda y paginación.

**Características:**
- Búsqueda por nombre o siglas
- Paginación de resultados
- Acciones rápidas (ver, editar, eliminar)
- Estados de carga y error
- Confirmación para eliminación

### CreateInstitutionPage

Página para crear nuevas instituciones.

**Flujo:**
1. Usuario llena el formulario
2. Validación en tiempo real
3. Envío al backend
4. Redirección a lista con mensaje de éxito

### EditInstitutionPage

Página para editar instituciones existentes.

**Flujo:**
1. Carga de datos de la institución
2. Prellenado del formulario
3. Validación y envío
4. Redirección con confirmación

### InstitutionDetailPage

Página de detalle que muestra información completa de una institución.

**Secciones:**
- Información general
- Acciones rápidas
- Botones de edición y eliminación

## Rutas

| Ruta | Componente | Descripción |
|------|------------|-------------|
| `/instituciones` | `InstitutionsListPage` | Lista de instituciones |
| `/instituciones/crear` | `CreateInstitutionPage` | Crear institución |
| `/instituciones/:id` | `InstitutionDetailPage` | Detalle de institución |
| `/instituciones/:id/editar` | `EditInstitutionPage` | Editar institución |

## API Endpoints

### Backend (Laravel)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/api/v1/instituciones` | Listar instituciones |
| `POST` | `/api/v1/instituciones` | Crear institución |
| `GET` | `/api/v1/instituciones/{id}` | Obtener institución |
| `PUT` | `/api/v1/instituciones/{id}` | Actualizar institución |
| `DELETE` | `/api/v1/instituciones/{id}` | Eliminar institución |

### Parámetros de Consulta

- `page`: Número de página (default: 1)
- `per_page`: Elementos por página (default: 10)
- `search`: Término de búsqueda

## Integración con el Sistema

### Relaciones

La institución es una entidad central que se relaciona con:

- **Usuarios**: Cada usuario pertenece a una institución
- **Grados**: Los grados están asociados a una institución
- **Áreas**: Las áreas académicas pertenecen a una institución
- **Aulas**: Las aulas están asociadas a una institución
- **Franjas Horarias**: Configuradas por institución

### Menú de Navegación

El módulo aparece en la sección **PRINCIPAL** del menú lateral:

```
PRINCIPAL
├── Dashboard
└── Instituciones  ← Nuevo módulo
```

## Estados y Manejo de Errores

### Estados de Carga

- **Loading**: Muestra spinner durante operaciones
- **Error**: Mensajes de error contextuales
- **Success**: Confirmaciones de operaciones exitosas

### Validaciones

**Frontend:**
- Campos requeridos
- Longitud mínima y máxima
- Formato de datos

**Backend:**
- Validación de datos
- Verificación de permisos
- Manejo de errores de base de datos

## Responsive Design

### Breakpoints

- **Mobile**: < 768px
- **Tablet**: 768px - 1024px
- **Desktop**: > 1024px

### Adaptaciones

- Menú colapsable en móviles
- Tablas con scroll horizontal
- Botones adaptados al tamaño de pantalla
- Formularios optimizados para touch

## Accesibilidad

### Características

- Navegación por teclado
- Etiquetas ARIA apropiadas
- Contraste de colores adecuado
- Mensajes de error claros
- Estados de foco visibles

## Testing

### Casos de Prueba

1. **Creación de institución**
   - Formulario válido
   - Formulario inválido
   - Errores de red

2. **Edición de institución**
   - Carga de datos
   - Actualización exitosa
   - Validaciones

3. **Eliminación de institución**
   - Confirmación
   - Cancelación
   - Errores

4. **Búsqueda y filtrado**
   - Términos válidos
   - Resultados vacíos
   - Paginación

## Mantenimiento

### Consideraciones

- **Escalabilidad**: El módulo está preparado para manejar múltiples instituciones
- **Performance**: Paginación y búsqueda optimizadas
- **Seguridad**: Validaciones tanto en frontend como backend
- **UX**: Feedback inmediato y navegación intuitiva

### Futuras Mejoras

- [ ] Filtros avanzados
- [ ] Exportación de datos
- [ ] Importación masiva
- [ ] Historial de cambios
- [ ] Configuraciones por institución
- [ ] Dashboard específico por institución

## Dependencias

### Frontend
- React Router DOM
- Axios
- Tailwind CSS
- React Hook Form (futuro)

### Backend
- Laravel 10
- Eloquent ORM
- Sanctum Authentication
- API Resources

## Configuración

### Variables de Entorno

```env
# API Configuration
VITE_API_BASE_URL=http://localhost:8000/api/v1
VITE_APP_NAME="Sistema Académico"
```

### Permisos

El módulo requiere los siguientes permisos:

- `instituciones.view` - Ver instituciones
- `instituciones.create` - Crear instituciones
- `instituciones.edit` - Editar instituciones
- `instituciones.delete` - Eliminar instituciones

## Troubleshooting

### Problemas Comunes

1. **Error 404 al cargar institución**
   - Verificar que el ID existe
   - Revisar permisos del usuario

2. **Error de validación**
   - Verificar formato de datos
   - Revisar reglas de validación

3. **Problemas de búsqueda**
   - Verificar conexión a API
   - Revisar parámetros de consulta

### Logs

Los errores se registran en:
- **Frontend**: Console del navegador
- **Backend**: `storage/logs/laravel.log`

## Contribución

### Guías de Desarrollo

1. Seguir el patrón de componentes establecido
2. Mantener consistencia en el diseño
3. Agregar validaciones apropiadas
4. Documentar cambios importantes
5. Probar en diferentes dispositivos

### Estándares de Código

- TypeScript para tipado
- ESLint para linting
- Prettier para formateo
- Conventional Commits para mensajes 