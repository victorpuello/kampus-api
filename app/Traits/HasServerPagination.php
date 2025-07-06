<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait HasServerPagination
{
    /**
     * Aplica paginación y ordenamiento del servidor a una consulta
     *
     * @param Builder $query
     * @param Request $request
     * @param array $searchableColumns Columnas en las que buscar
     * @param array $defaultOrder Ordenamiento por defecto ['column' => 'direction']
     * @param array $additionalFilters Filtros adicionales a aplicar
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function applyServerPagination(
        Builder $query,
        Request $request,
        array $searchableColumns = [],
        array $defaultOrder = ['id' => 'desc'],
        array $additionalFilters = []
    ) {
        // Aplicar filtros adicionales
        foreach ($additionalFilters as $filter) {
            if (isset($filter['column']) && isset($filter['value'])) {
                $query->where($filter['column'], $filter['value']);
            }
        }

        // Aplicar búsqueda si se especifica
        if ($request->search && !empty($searchableColumns)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchableColumns, $searchTerm) {
                foreach ($searchableColumns as $column) {
                    $q->orWhere($column, 'like', "%{$searchTerm}%");
                }
            });
        }

        // Aplicar ordenamiento
        if ($request->sort_by) {
            $direction = $request->sort_direction === 'desc' ? 'desc' : 'asc';
            
            // Verificar si es un campo calculado (withCount)
            if (str_contains($request->sort_by, '_count')) {
                // Para campos calculados, usar orderByRaw para evitar errores SQL
                $query->orderByRaw("{$request->sort_by} {$direction}");
            } else {
                $query->orderBy($request->sort_by, $direction);
            }
        } else {
            // Aplicar ordenamiento por defecto
            foreach ($defaultOrder as $column => $direction) {
                if (str_contains($column, '_count')) {
                    $query->orderByRaw("{$column} {$direction}");
                } else {
                    $query->orderBy($column, $direction);
                }
            }
        }

        // Aplicar paginación
        $perPage = $request->per_page ?? 10;
        return $query->paginate($perPage);
    }

    /**
     * Aplica paginación y ordenamiento del servidor con relaciones
     *
     * @param Builder $query
     * @param Request $request
     * @param array $with Relaciones a cargar
     * @param array $searchableColumns Columnas en las que buscar
     * @param array $defaultOrder Ordenamiento por defecto
     * @param array $additionalFilters Filtros adicionales
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function applyServerPaginationWithRelations(
        Builder $query,
        Request $request,
        array $with = [],
        array $searchableColumns = [],
        array $defaultOrder = ['id' => 'desc'],
        array $additionalFilters = []
    ) {
        // Cargar relaciones
        if (!empty($with)) {
            $query->with($with);
        }

        return $this->applyServerPagination($query, $request, $searchableColumns, $defaultOrder, $additionalFilters);
    }

    /**
     * Aplica paginación y ordenamiento del servidor con conteo de relaciones
     *
     * @param Builder $query
     * @param Request $request
     * @param array $withCount Relaciones a contar
     * @param array $searchableColumns Columnas en las que buscar
     * @param array $defaultOrder Ordenamiento por defecto
     * @param array $additionalFilters Filtros adicionales
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function applyServerPaginationWithCount(
        Builder $query,
        Request $request,
        array $withCount = [],
        array $searchableColumns = [],
        array $defaultOrder = ['id' => 'desc'],
        array $additionalFilters = []
    ) {
        // Agregar conteo de relaciones
        if (!empty($withCount)) {
            $query->withCount($withCount);
        }

        return $this->applyServerPagination($query, $request, $searchableColumns, $defaultOrder, $additionalFilters);
    }

    /**
     * Aplica paginación y ordenamiento del servidor con relaciones y conteo
     *
     * @param Builder $query
     * @param Request $request
     * @param array $with Relaciones a cargar
     * @param array $withCount Relaciones a contar
     * @param array $searchableColumns Columnas en las que buscar
     * @param array $defaultOrder Ordenamiento por defecto
     * @param array $additionalFilters Filtros adicionales
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function applyServerPaginationWithRelationsAndCount(
        Builder $query,
        Request $request,
        array $with = [],
        array $withCount = [],
        array $searchableColumns = [],
        array $defaultOrder = ['id' => 'desc'],
        array $additionalFilters = []
    ) {
        // Cargar relaciones
        if (!empty($with)) {
            $query->with($with);
        }

        // Agregar conteo de relaciones
        if (!empty($withCount)) {
            $query->withCount($withCount);
        }

        return $this->applyServerPagination($query, $request, $searchableColumns, $defaultOrder, $additionalFilters);
    }
} 