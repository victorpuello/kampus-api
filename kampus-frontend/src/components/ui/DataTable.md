# DataTable Component

Un componente reutilizable de tabla de datos con funcionalidades avanzadas como búsqueda, ordenamiento, paginación y acciones en lote.

## Características

- ✅ **Búsqueda**: Filtrado en tiempo real por múltiples campos
- ✅ **Ordenamiento**: Ordenamiento por columnas con indicadores visuales
- ✅ **Paginación**: Navegación entre páginas con opciones de elementos por página
- ✅ **Acciones**: Botones de acción por fila con iconos y variantes
- ✅ **Selección**: Checkboxes para selección individual y en lote
- ✅ **Acciones en lote**: Operaciones sobre múltiples elementos seleccionados
- ✅ **Responsive**: Diseño adaptable a diferentes tamaños de pantalla
- ✅ **Loading states**: Estados de carga y error
- ✅ **Personalizable**: Altamente configurable con props

## Uso Básico

```tsx
import { DataTable } from '../components/ui/DataTable';
import type { Column, ActionButton } from '../components/ui/DataTable';

interface User {
  id: number;
  name: string;
  email: string;
  role: string;
}

const MyComponent = () => {
  const [users, setUsers] = useState<User[]>([]);
  const [loading, setLoading] = useState(false);

  // Definir columnas
  const columns: Column<User>[] = [
    {
      key: 'name',
      header: 'Nombre',
      accessor: (user) => user.name,
      sortable: true,
    },
    {
      key: 'email',
      header: 'Email',
      accessor: (user) => user.email,
      sortable: true,
    },
    {
      key: 'role',
      header: 'Rol',
      accessor: (user) => user.role,
      sortable: true,
    },
  ];

  // Definir acciones
  const actions: ActionButton<User>[] = [
    {
      label: 'Editar',
      variant: 'ghost',
      size: 'sm',
      onClick: (user) => handleEdit(user),
      icon: <EditIcon />,
    },
    {
      label: 'Eliminar',
      variant: 'ghost',
      size: 'sm',
      onClick: (user) => handleDelete(user),
      icon: <DeleteIcon />,
    },
  ];

  return (
    <DataTable
      data={users}
      columns={columns}
      actions={actions}
      loading={loading}
      searchable={true}
      searchKeys={['name', 'email']}
      sortable={true}
      pagination={true}
    />
  );
};
```

## Props

### DataTableProps<T>

| Prop | Tipo | Default | Descripción |
|------|------|---------|-------------|
| `data` | `T[]` | - | Array de datos a mostrar |
| `columns` | `Column<T>[]` | - | Configuración de columnas |
| `actions` | `ActionButton<T>[]` | `[]` | Botones de acción por fila |
| `loading` | `boolean` | `false` | Estado de carga |
| `error` | `string \| null` | `null` | Mensaje de error |
| `searchable` | `boolean` | `true` | Habilitar búsqueda |
| `searchPlaceholder` | `string` | `'Buscar...'` | Placeholder del campo de búsqueda |
| `searchKeys` | `(keyof T)[]` | `[]` | Campos para buscar |
| `sortable` | `boolean` | `true` | Habilitar ordenamiento |
| `pagination` | `boolean` | `true` | Habilitar paginación |
| `itemsPerPage` | `number` | `10` | Elementos por página |
| `itemsPerPageOptions` | `number[]` | `[5, 10, 25, 50]` | Opciones de elementos por página |
| `emptyMessage` | `string` | `'No hay datos disponibles'` | Mensaje cuando no hay datos |
| `emptyIcon` | `ReactNode` | - | Icono para estado vacío |
| `className` | `string` | - | Clases CSS adicionales |
| `onRowClick` | `(item: T) => void` | - | Callback al hacer clic en fila |
| `selectable` | `boolean` | `false` | Habilitar selección |
| `onSelectionChange` | `(selectedItems: T[]) => void` | - | Callback al cambiar selección |
| `bulkActions` | `ActionButton<T[]>[]` | `[]` | Acciones en lote |

### Column<T>

| Prop | Tipo | Default | Descripción |
|------|------|---------|-------------|
| `key` | `string` | - | Identificador único de la columna |
| `header` | `string` | - | Título de la columna |
| `accessor` | `(item: T) => ReactNode` | - | Función para renderizar el contenido |
| `sortable` | `boolean` | `true` | Si la columna es ordenable |
| `width` | `string` | - | Ancho de la columna |
| `align` | `'left' \| 'center' \| 'right'` | `'left'` | Alineación del contenido |

### ActionButton<T>

| Prop | Tipo | Default | Descripción |
|------|------|---------|-------------|
| `label` | `string` | - | Texto del botón |
| `icon` | `ReactNode` | - | Icono del botón |
| `variant` | `'primary' \| 'secondary' \| 'danger' \| 'success' \| 'ghost'` | `'ghost'` | Variante del botón |
| `size` | `'sm' \| 'md' \| 'lg'` | `'sm'` | Tamaño del botón |
| `onClick` | `(item: T) => void` | - | Función al hacer clic |
| `disabled` | `(item: T) => boolean` | - | Función para deshabilitar |
| `hidden` | `(item: T) => boolean` | - | Función para ocultar |

## Ejemplos Avanzados

### Con Selección y Acciones en Lote

```tsx
const bulkActions: ActionButton<User[]>[] = [
  {
    label: 'Eliminar Seleccionados',
    variant: 'danger',
    size: 'sm',
    onClick: (selectedUsers) => handleBulkDelete(selectedUsers),
    icon: <DeleteIcon />,
  },
  {
    label: 'Exportar',
    variant: 'secondary',
    size: 'sm',
    onClick: (selectedUsers) => handleExport(selectedUsers),
    icon: <ExportIcon />,
  },
];

<DataTable
  data={users}
  columns={columns}
  actions={actions}
  selectable={true}
  bulkActions={bulkActions}
  onSelectionChange={(selected) => console.log('Seleccionados:', selected)}
/>
```

### Con Acceso a Datos Anidados

```tsx
const columns: Column<User>[] = [
  {
    key: 'profile',
    header: 'Usuario',
    accessor: (user) => (
      <div className="flex items-center">
        <img src={user.profile.avatar} className="w-8 h-8 rounded-full" />
        <div className="ml-3">
          <div className="font-medium">{user.profile.name}</div>
          <div className="text-sm text-gray-500">{user.profile.title}</div>
        </div>
      </div>
    ),
  },
  {
    key: 'department',
    header: 'Departamento',
    accessor: (user) => user.department.name,
    sortable: true,
  },
];
```

### Con Estados Condicionales

```tsx
const actions: ActionButton<User>[] = [
  {
    label: 'Activar',
    variant: 'success',
    size: 'sm',
    onClick: (user) => handleActivate(user),
    hidden: (user) => user.status === 'active',
  },
  {
    label: 'Desactivar',
    variant: 'danger',
    size: 'sm',
    onClick: (user) => handleDeactivate(user),
    hidden: (user) => user.status === 'inactive',
  },
  {
    label: 'Editar',
    variant: 'ghost',
    size: 'sm',
    onClick: (user) => handleEdit(user),
    disabled: (user) => user.status === 'deleted',
  },
];
```

### Con Búsqueda Personalizada

```tsx
<DataTable
  data={users}
  columns={columns}
  searchable={true}
  searchKeys={['name', 'email', 'department.name', 'profile.title']}
  searchPlaceholder="Buscar usuarios por nombre, email o departamento..."
/>
```

## Estilos y Personalización

El componente utiliza Tailwind CSS y es completamente personalizable. Puedes:

- Modificar los colores usando las clases de Tailwind
- Agregar clases CSS personalizadas con la prop `className`
- Personalizar los iconos y componentes de UI
- Ajustar el espaciado y tamaños

## Consideraciones de Rendimiento

- El componente utiliza `useMemo` para optimizar el filtrado y ordenamiento
- La paginación se realiza en el cliente para mejor rendimiento
- Los datos se filtran y ordenan solo cuando es necesario
- Considera usar `React.memo` para componentes de acceso complejos

## Accesibilidad

- Soporte completo para navegación por teclado
- Roles ARIA apropiados
- Etiquetas y descripciones para lectores de pantalla
- Contraste de colores adecuado
- Indicadores de estado claros 