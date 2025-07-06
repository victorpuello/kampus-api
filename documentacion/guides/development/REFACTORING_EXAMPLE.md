# Ejemplo de Refactorización - Paginación del Servidor

Este documento muestra cómo refactorizar una página existente para usar la nueva configuración permanente de paginación del servidor.

## 🔄 Antes de la Refactorización

### Frontend - Página Original

```typescript
// AcademicYearsListPage.tsx (ANTES)
import { useEffect, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import axiosClient from '../api/axiosClient';
import { DataTable } from '../components/ui/DataTable';

interface Anio {
  id: number;
  nombre: string;
  descripcion?: string;
  estado: string;
}

interface AniosResponse {
  data: Anio[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number;
  to: number;
}

const AcademicYearsListPage = () => {
  const navigate = useNavigate();
  const [anios, setAnios] = useState<Anio[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  
  // Estados para paginación del servidor
  const [currentPage, setCurrentPage] = useState(1);
  const [itemsPerPage, setItemsPerPage] = useState(10);
  const [searchTerm, setSearchTerm] = useState('');
  const [totalItems, setTotalItems] = useState(0);
  const [totalPages, setTotalPages] = useState(0);
  const [sortColumn, setSortColumn] = useState<string | null>(null);
  const [sortDirection, setSortDirection] = useState<'asc' | 'desc'>('asc');

  const fetchAnios = async (page = 1, perPage = 10, search = '', sortBy?: string, sortDir?: 'asc' | 'desc') => {
    try {
      setLoading(true);
      const params = new URLSearchParams({
        page: page.toString(),
        per_page: perPage.toString(),
      });
      
      if (search) {
        params.append('search', search);
      }

      if (sortBy) {
        params.append('sort_by', sortBy);
        params.append('sort_direction', sortDir || 'asc');
      }
      
      const response = await axiosClient.get(`/anios?${params.toString()}`);
      const data: AniosResponse = response.data;
      
      setAnios(data.data);
      setTotalItems(data.meta.total);
      setTotalPages(data.meta.last_page);
      setCurrentPage(data.meta.current_page);
      setItemsPerPage(data.meta.per_page);
      setError(null);
    } catch (err: any) {
      setError(err.response?.data?.message || 'Error al cargar los años académicos');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchAnios(currentPage, itemsPerPage, searchTerm, sortColumn || undefined, sortDirection);
  }, [currentPage, itemsPerPage, searchTerm, sortColumn, sortDirection]);

  const handlePageChange = (page: number) => {
    setCurrentPage(page);
  };

  const handleItemsPerPageChange = (perPage: number) => {
    setItemsPerPage(perPage);
    setCurrentPage(1);
  };

  const handleSearch = (search: string) => {
    setSearchTerm(search);
    setCurrentPage(1);
  };

  const handleSort = (columnKey: string, direction: 'asc' | 'desc') => {
    setSortColumn(columnKey);
    setSortDirection(direction);
    setCurrentPage(1);
  };

  // ... resto del código (columnas, acciones, etc.)

  return (
    <div className="space-y-6">
      <DataTable
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
        bulkActions={bulkActions}
        onRowClick={(anio) => navigate(`/anios/${anio.id}`)}
        // Props para paginación del servidor
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
```

### Backend - Controlador Original

```php
// AnioController.php (ANTES)
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnioResource;
use App\Models\Anio;
use Illuminate\Http\Request;

class AnioController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Anio::query()
            ->where('institucion_id', $user->institucion_id)
            ->when($request->search, function ($query, $search) {
                $query->where('nombre', 'like', "%{$search}%");
            });

        // Aplicar ordenamiento si se especifica
        if ($request->sort_by) {
            $direction = $request->sort_direction === 'desc' ? 'desc' : 'asc';
            $query->orderBy($request->sort_by, $direction);
        } else {
            // Ordenamiento por defecto
            $query->orderBy('nombre', 'asc');
        }

        $anios = $query->paginate($request->per_page ?? 10);

        return AnioResource::collection($anios);
    }
}
```

## ✅ Después de la Refactorización

### Frontend - Página Refactorizada

```typescript
// AcademicYearsListPage.tsx (DESPUÉS)
import { Link, useNavigate } from 'react-router-dom';
import { useServerPagination } from '../hooks/useServerPagination';
import { ServerDataTable } from '../components/ui/ServerDataTable';
import type { Column, ActionButton } from '../components/ui/DataTable';

interface Anio {
  id: number;
  nombre: string;
  descripcion?: string;
  estado: string;
}

const AcademicYearsListPage = () => {
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

  // ... columnas y acciones (sin cambios)

  return (
    <div className="space-y-6">
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
    </div>
  );
};
```

### Backend - Controlador Refactorizado

```php
// AnioController.php (DESPUÉS)
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

## 📊 Comparación de Código

| Aspecto | Antes | Después | Reducción |
|---------|-------|---------|-----------|
| **Líneas de código Frontend** | ~150 líneas | ~50 líneas | **67% menos** |
| **Líneas de código Backend** | ~30 líneas | ~15 líneas | **50% menos** |
| **Estados manuales** | 7 estados | 0 estados | **100% menos** |
| **Funciones manuales** | 4 funciones | 0 funciones | **100% menos** |
| **useEffect manual** | 1 useEffect | 0 useEffect | **100% menos** |
| **Lógica de paginación** | Repetida | Reutilizable | **DRY** |

## 🎯 Beneficios de la Refactorización

### ✅ **Reducción de Código**
- **Frontend**: De ~150 líneas a ~50 líneas (67% menos)
- **Backend**: De ~30 líneas a ~15 líneas (50% menos)

### ✅ **Eliminación de Duplicación**
- No más estados manuales para paginación
- No más funciones manuales para handlers
- No más useEffect manual para sincronización

### ✅ **Consistencia**
- Misma estructura en todas las páginas
- Mismos parámetros de API en todos los controladores
- Mismo comportamiento de paginación

### ✅ **Mantenibilidad**
- Cambios centralizados en hooks y traits
- Fácil agregar nuevas funcionalidades
- Menos puntos de falla

### ✅ **Reutilización**
- Hook `useServerPagination` reutilizable
- Trait `HasServerPagination` reutilizable
- Componente `ServerDataTable` reutilizable

## 🚀 Pasos para Refactorizar

### 1. **Frontend**
```bash
# 1. Importar el hook y componente
import { useServerPagination } from '../hooks/useServerPagination';
import { ServerDataTable } from '../components/ui/ServerDataTable';

# 2. Reemplazar estados manuales con el hook
const { data, loading, error, ...handlers } = useServerPagination({
  endpoint: '/api/v1/tu-endpoint',
  searchKeys: ['columna1', 'columna2'],
});

# 3. Reemplazar DataTable con ServerDataTable
<ServerDataTable
  data={data}
  // ... resto de props
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

### 2. **Backend**
```bash
# 1. Agregar el trait
use App\Traits\HasServerPagination;

class TuController extends Controller
{
    use HasServerPagination;

# 2. Reemplazar lógica manual con el trait
$resultados = $this->applyServerPagination(
    $query,
    $request,
    ['columna1', 'columna2'], // Columnas buscables
    ['columna1' => 'asc'] // Ordenamiento por defecto
);
```

## 🔧 Casos Especiales

### Con Relaciones
```php
// Backend
$resultados = $this->applyServerPaginationWithRelations(
    $query,
    $request,
    ['institucion', 'periodos'], // Relaciones
    ['nombre', 'descripcion'], // Columnas buscables
    ['nombre' => 'asc'] // Ordenamiento por defecto
);
```

### Con Conteo
```php
// Backend
$resultados = $this->applyServerPaginationWithCount(
    $query,
    $request,
    ['estudiantes', 'docentes'], // Conteo
    ['nombre', 'descripcion'], // Columnas buscables
    ['nombre' => 'asc'] // Ordenamiento por defecto
);
```

### Con Parámetros Adicionales
```typescript
// Frontend
const { data, ... } = useServerPagination({
  endpoint: '/api/v1/anios',
  searchKeys: ['nombre', 'descripcion'],
  additionalParams: {
    estado: 'activo',
    institucion_id: user.institucion_id
  }
});
```

## ✅ Checklist de Refactorización

- [ ] **Frontend**
  - [ ] Importar `useServerPagination`
  - [ ] Importar `ServerDataTable`
  - [ ] Reemplazar estados manuales con el hook
  - [ ] Reemplazar `DataTable` con `ServerDataTable`
  - [ ] Eliminar funciones manuales de handlers
  - [ ] Eliminar `useEffect` manual
  - [ ] Eliminar interfaz de respuesta manual

- [ ] **Backend**
  - [ ] Agregar `use App\Traits\HasServerPagination;`
  - [ ] Agregar `use HasServerPagination;` en la clase
  - [ ] Reemplazar lógica manual con `applyServerPagination()`
  - [ ] Eliminar código de ordenamiento manual
  - [ ] Eliminar código de búsqueda manual

- [ ] **Verificación**
  - [ ] Compilar frontend sin errores
  - [ ] Probar paginación
  - [ ] Probar búsqueda
  - [ ] Probar ordenamiento
  - [ ] Probar cambio de elementos por página

Esta refactorización asegura que todas las páginas de lista del sistema usen la configuración permanente de paginación del servidor, evitando errores futuros y manteniendo consistencia en toda la aplicación. 