# Guía de Paginación del Servidor - Kampus API

Esta guía explica cómo usar la configuración permanente de paginación del servidor implementada en Kampus API para evitar errores futuros y mantener consistencia en toda la aplicación.

## 📋 Índice

1. [Componentes Frontend](#componentes-frontend)
2. [Trait Laravel](#trait-laravel)
3. [Implementación en Controladores](#implementación-en-controladores)
4. [Ejemplos de Uso](#ejemplos-de-uso)
5. [Mejores Prácticas](#mejores-prácticas)

## 🎯 Componentes Frontend

### Hook `useServerPagination`

El hook `useServerPagination` maneja automáticamente toda la lógica de paginación del servidor:

```typescript
import { useServerPagination } from '../hooks/useServerPagination';

// En tu componente
const {
  data,
  loading,
  error,
  currentPage,
  itemsPerPage,
  totalItems,
  totalPages,
  searchTerm,
  sortColumn,
  sortDirection,
  handlePageChange,
  handleItemsPerPageChange,
  handleSearch,
  handleSort,
  refreshData,
  clearError,
} = useServerPagination({
  endpoint: '/api/v1/anios',
  initialPage: 1,
  initialPerPage: 10,
  initialSearch: '',
  initialSortColumn: null,
  initialSortDirection: 'asc',
  searchKeys: ['nombre', 'descripcion', 'estado'],
  additionalParams: {
    // Parámetros adicionales si es necesario
  }
});
```

### Componente `ServerDataTable`

El componente `ServerDataTable` es un wrapper que incluye automáticamente todas las props necesarias para paginación del servidor:

```typescript
import { ServerDataTable } from '../components/ui/ServerDataTable';

// En tu componente
<ServerDataTable
  data={data}
  columns={columns}
  actions={actions}
  loading={loading}
  error={error}
  searchable={true}
  searchPlaceholder="Buscar años académicos..."
  searchKeys={['nombre', 'descripcion', 'estado']}
  sortable={true}
  pagination={true}
  itemsPerPage={itemsPerPage}
  itemsPerPageOptions={[5, 10, 25, 50]}
  emptyMessage="No hay años académicos registrados"
  selectable={true}
  bulkActions={bulkActions}
  onRowClick={(anio) => navigate(`/anios/${anio.id}`)}
  // Props automáticas para paginación del servidor
  serverSidePagination={true}
  currentPage={currentPage}
  totalPages={totalPages}
  totalItems={totalItems}
  onPageChange={handlePageChange}
  onItemsPerPageChange={handleItemsPerPageChange}
  onSearch={handleSearch}
  onSort={handleSort}
/>
```

## 🔧 Trait Laravel

### Trait `HasServerPagination`

El trait `HasServerPagination` proporciona métodos para manejar automáticamente la paginación y ordenamiento del servidor:

```php
use App\Traits\HasServerPagination;

class TuController extends Controller
{
    use HasServerPagination;

    public function index(Request $request)
    {
        $query = TuModelo::query();
        
        $resultados = $this->applyServerPagination(
            $query,
            $request,
            ['nombre', 'descripcion'], // Columnas buscables
            ['nombre' => 'asc'] // Ordenamiento por defecto
        );

        return TuResource::collection($resultados);
    }
}
```

### Métodos Disponibles

#### `applyServerPagination()`
Método básico para paginación y ordenamiento:

```php
$resultados = $this->applyServerPagination(
    $query,
    $request,
    ['nombre', 'descripcion'], // Columnas buscables
    ['nombre' => 'asc'], // Ordenamiento por defecto
    [ // Filtros adicionales
        ['column' => 'estado', 'value' => 'activo']
    ]
);
```

#### `applyServerPaginationWithRelations()`
Incluye carga de relaciones:

```php
$resultados = $this->applyServerPaginationWithRelations(
    $query,
    $request,
    ['institucion', 'periodos'], // Relaciones a cargar
    ['nombre', 'descripcion'], // Columnas buscables
    ['nombre' => 'asc'] // Ordenamiento por defecto
);
```

#### `applyServerPaginationWithCount()`
Incluye conteo de relaciones:

```php
$resultados = $this->applyServerPaginationWithCount(
    $query,
    $request,
    ['estudiantes', 'docentes'], // Relaciones a contar
    ['nombre', 'descripcion'], // Columnas buscables
    ['nombre' => 'asc'] // Ordenamiento por defecto
);
```

#### `applyServerPaginationWithRelationsAndCount()`
Combina relaciones y conteo:

```php
$resultados = $this->applyServerPaginationWithRelationsAndCount(
    $query,
    $request,
    ['institucion'], // Relaciones a cargar
    ['estudiantes', 'docentes'], // Relaciones a contar
    ['nombre', 'descripcion'], // Columnas buscables
    ['nombre' => 'asc'] // Ordenamiento por defecto
);
```

## 🚀 Implementación en Controladores

### Ejemplo Completo

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnioResource;
use App\Models\Anio;
use App\Traits\HasServerPagination;
use Illuminate\Http\Request;

class AnioController extends Controller
{
    use HasServerPagination;

    public function __construct()
    {
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':ver_anios')->only(['index', 'show']);
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':crear_anios')->only(['store']);
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':editar_anios')->only(['update']);
        $this->middleware(\App\Http\Middleware\CheckPermission::class.':eliminar_anios')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Anio::query()
            ->where('institucion_id', $user->institucion_id);

        $anios = $this->applyServerPagination(
            $query,
            $request,
            ['nombre', 'descripcion', 'estado'], // Columnas buscables
            ['nombre' => 'asc'] // Ordenamiento por defecto
        );

        return AnioResource::collection($anios);
    }
}
```

## 📝 Ejemplos de Uso

### Frontend - Página de Lista

```typescript
import React from 'react';
import { useNavigate } from 'react-router-dom';
import { useServerPagination } from '../hooks/useServerPagination';
import { ServerDataTable } from '../components/ui/ServerDataTable';
import type { Column, ActionButton } from '../components/ui/DataTable';

interface Anio {
  id: number;
  nombre: string;
  descripcion?: string;
  estado: string;
}

const AniosListPage = () => {
  const navigate = useNavigate();
  
  const {
    data: anios,
    loading,
    error,
    currentPage,
    itemsPerPage,
    totalItems,
    totalPages,
    handlePageChange,
    handleItemsPerPageChange,
    handleSearch,
    handleSort,
    refreshData,
  } = useServerPagination<Anio>({
    endpoint: '/api/v1/anios',
    searchKeys: ['nombre', 'descripcion', 'estado'],
  });

  const columns: Column<Anio>[] = [
    {
      key: 'nombre',
      header: 'Año Académico',
      accessor: (anio) => anio.nombre,
      sortable: true,
    },
    {
      key: 'estado',
      header: 'Estado',
      accessor: (anio) => anio.estado,
      sortable: true,
    },
  ];

  const actions: ActionButton<Anio>[] = [
    {
      label: 'Ver',
      variant: 'ghost',
      size: 'sm',
      onClick: (anio) => navigate(`/anios/${anio.id}`),
    },
    {
      label: 'Editar',
      variant: 'ghost',
      size: 'sm',
      onClick: (anio) => navigate(`/anios/${anio.id}/editar`),
    },
  ];

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold">Años Académicos</h1>
        <Button onClick={() => navigate('/anios/crear')}>
          Agregar Año
        </Button>
      </div>

      <ServerDataTable
        data={anios}
        columns={columns}
        actions={actions}
        loading={loading}
        error={error}
        searchable={true}
        searchPlaceholder="Buscar años académicos..."
        searchKeys={['nombre', 'descripcion', 'estado']}
        sortable={true}
        pagination={true}
        itemsPerPage={itemsPerPage}
        itemsPerPageOptions={[5, 10, 25, 50]}
        emptyMessage="No hay años académicos registrados"
        selectable={true}
        onRowClick={(anio) => navigate(`/anios/${anio.id}`)}
        // Props automáticas para paginación del servidor
        serverSidePagination={true}
        currentPage={currentPage}
        totalPages={totalPages}
        totalItems={totalItems}
        onPageChange={handlePageChange}
        onItemsPerPageChange={handleItemsPerPageChange}
        onSearch={handleSearch}
        onSort={handleSort}
      />
    </div>
  );
};

export default AniosListPage;
```

## ✅ Mejores Prácticas

### Frontend

1. **Siempre usa `useServerPagination`** para listas paginadas
2. **Usa `ServerDataTable`** en lugar de `DataTable` para listas del servidor
3. **Define interfaces TypeScript** para tus modelos
4. **Maneja errores** con el estado `error` del hook
5. **Usa `refreshData()`** después de operaciones CRUD

### Backend

1. **Siempre usa el trait `HasServerPagination`** en controladores de listas
2. **Define columnas buscables** apropiadas para cada modelo
3. **Establece ordenamiento por defecto** lógico
4. **Documenta parámetros** en la documentación OpenAPI
5. **Aplica filtros de seguridad** (ej: `institucion_id`)

### Parámetros de API

Los controladores que usan el trait soportan automáticamente estos parámetros:

- `page`: Número de página (default: 1)
- `per_page`: Elementos por página (default: 10)
- `search`: Término de búsqueda
- `sort_by`: Columna para ordenar
- `sort_direction`: Dirección del ordenamiento (`asc` o `desc`)

### Estructura de Respuesta

Todas las respuestas siguen esta estructura estándar de Laravel:

```json
{
  "data": [...],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 10,
    "total": 50,
    "from": 1,
    "to": 10
  },
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": "..."
  }
}
```

## 🔄 Migración de Código Existente

Para migrar controladores existentes:

1. **Agregar el trait**:
   ```php
   use App\Traits\HasServerPagination;
   
   class TuController extends Controller
   {
       use HasServerPagination;
   ```

2. **Reemplazar lógica de paginación**:
   ```php
   // Antes
   $query = Modelo::query()
       ->when($request->search, function ($query, $search) {
           $query->where('nombre', 'like', "%{$search}%");
       });
   
   if ($request->sort_by) {
       $query->orderBy($request->sort_by, $request->sort_direction ?? 'asc');
   }
   
   $resultados = $query->paginate($request->per_page ?? 10);
   
   // Después
   $query = Modelo::query();
   
   $resultados = $this->applyServerPagination(
       $query,
       $request,
       ['nombre'], // Columnas buscables
       ['nombre' => 'asc'] // Ordenamiento por defecto
   );
   ```

3. **Actualizar frontend** para usar `useServerPagination` y `ServerDataTable`

## 🎯 Beneficios

- ✅ **Consistencia** en toda la aplicación
- ✅ **Menos errores** de implementación
- ✅ **Código más limpio** y mantenible
- ✅ **Reutilización** de lógica común
- ✅ **Documentación automática** de parámetros
- ✅ **Fácil migración** de código existente

Esta configuración permanente asegura que todas las DataTables del sistema funcionen correctamente con paginación del servidor, búsqueda y ordenamiento. 