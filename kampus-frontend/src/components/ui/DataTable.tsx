import React, { useState, useMemo } from 'react';
import { Button } from './Button';
import { Input } from './Input';
import { Badge } from './Badge';
import { LoadingSpinner } from './LoadingSpinner';
import { cn } from '../../utils/cn';

export interface Column<T> {
  key: string;
  header: string;
  accessor: (item: T) => React.ReactNode;
  sortable?: boolean;
  width?: string;
  align?: 'left' | 'center' | 'right';
}

export interface ActionButton<T> {
  label: string;
  icon?: React.ReactNode;
  variant?: 'primary' | 'secondary' | 'danger' | 'success' | 'ghost';
  size?: 'sm' | 'md' | 'lg';
  onClick: (item: T) => void;
  disabled?: (item: T) => boolean;
  hidden?: (item: T) => boolean;
}

export interface DataTableProps<T> {
  data: T[];
  columns: Column<T>[];
  actions?: ActionButton<T>[];
  loading?: boolean;
  error?: string | null;
  searchable?: boolean;
  searchPlaceholder?: string;
  searchKeys?: string[];
  sortable?: boolean;
  pagination?: boolean;
  itemsPerPage?: number;
  itemsPerPageOptions?: number[];
  emptyMessage?: string;
  emptyIcon?: React.ReactNode;
  className?: string;
  onRowClick?: (item: T) => void;
  selectable?: boolean;
  onSelectionChange?: (selectedItems: T[]) => void;
  bulkActions?: ActionButton<T[]>[];
}

// Definir el tipo base para elementos con ID
interface BaseItem {
  id?: number | string;
}

// Componente DataTable corregido
export const DataTable = <T extends BaseItem>({
  data,
  columns,
  actions = [],
  loading = false,
  error = null,
  searchable = true,
  searchPlaceholder = 'Buscar...',
  searchKeys = [],
  sortable = true,
  pagination = true,
  itemsPerPage = 10,
  itemsPerPageOptions = [5, 10, 25, 50],
  emptyMessage = 'No hay datos disponibles',
  emptyIcon,
  className,
  onRowClick,
  selectable = false,
  onSelectionChange,
  bulkActions = [],
}: DataTableProps<T>) => {
  const [searchTerm, setSearchTerm] = useState('');
  const [sortColumn, setSortColumn] = useState<string | null>(null);
  const [sortDirection, setSortDirection] = useState<'asc' | 'desc'>('asc');
  const [currentPage, setCurrentPage] = useState(1);
  const [itemsPerPageState, setItemsPerPageState] = useState(itemsPerPage);
  const [selectedItems, setSelectedItems] = useState<T[]>([]);

  // Filtrar datos por término de búsqueda
  const filteredData = useMemo(() => {
    if (!searchTerm || searchKeys.length === 0) return data;

    return data.filter((item) =>
      searchKeys.some((key) => {
        // Función para obtener el valor de una propiedad anidada
        const getNestedValue = (obj: any, path: string) => {
          return path.split('.').reduce((current, key) => {
            return current && current[key] !== undefined ? current[key] : null;
          }, obj);
        };

        const value = getNestedValue(item, key as string);
        if (value === null || value === undefined) return false;
        return String(value).toLowerCase().includes(searchTerm.toLowerCase());
      })
    );
  }, [data, searchTerm, searchKeys]);

  // Ordenar datos
  const sortedData = useMemo(() => {
    if (!sortable || !sortColumn) return filteredData;

    const column = columns.find((col) => col.key === sortColumn);
    if (!column) return filteredData;

    return [...filteredData].sort((a, b) => {
      const aValue = column.accessor(a);
      const bValue = column.accessor(b);

      // Convertir a string para comparación
      const aString = String(aValue).toLowerCase();
      const bString = String(bValue).toLowerCase();

      if (sortDirection === 'asc') {
        return aString.localeCompare(bString);
      } else {
        return bString.localeCompare(aString);
      }
    });
  }, [filteredData, sortColumn, sortDirection, sortable, columns]);

  // Paginar datos
  const paginatedData = useMemo(() => {
    if (!pagination) return sortedData;

    const startIndex = (currentPage - 1) * itemsPerPageState;
    const endIndex = startIndex + itemsPerPageState;
    return sortedData.slice(startIndex, endIndex);
  }, [sortedData, currentPage, itemsPerPageState, pagination]);

  // Calcular información de paginación
  const totalPages = Math.ceil(sortedData.length / itemsPerPageState);
  const startItem = (currentPage - 1) * itemsPerPageState + 1;
  const endItem = Math.min(currentPage * itemsPerPageState, sortedData.length);

  // Manejar ordenamiento
  const handleSort = (columnKey: string) => {
    if (sortColumn === columnKey) {
      setSortDirection(sortDirection === 'asc' ? 'desc' : 'asc');
    } else {
      setSortColumn(columnKey);
      setSortDirection('asc');
    }
  };

  // Manejar selección
  const handleSelectAll = (checked: boolean) => {
    if (checked) {
      setSelectedItems([...paginatedData]);
    } else {
      setSelectedItems([]);
    }
  };

  const handleSelectItem = (item: T, checked: boolean) => {
    if (checked) {
      setSelectedItems([...selectedItems, item]);
    } else {
      setSelectedItems(selectedItems.filter((selected) => selected.id !== item.id));
    }
  };

  // Notificar cambios en la selección
  React.useEffect(() => {
    onSelectionChange?.(selectedItems);
  }, [selectedItems, onSelectionChange]);

  // Resetear página cuando cambian los filtros
  React.useEffect(() => {
    setCurrentPage(1);
  }, [searchTerm, sortColumn, sortDirection]);

  if (loading) {
    return (
      <div className="flex justify-center items-center h-64">
        <LoadingSpinner size="xl" text="Cargando datos..." />
      </div>
    );
  }

  if (error) {
    return (
      <div className="rounded-md bg-red-50 p-4">
        <div className="flex">
          <svg className="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p className="text-sm text-red-800">{error}</p>
        </div>
      </div>
    );
  }

  return (
    <div className={cn('space-y-4', className)}>
      {/* Header con búsqueda y acciones */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        {searchable && (
          <div className="flex-1 max-w-sm">
            <Input
              placeholder={searchPlaceholder}
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              leftIcon={
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              }
            />
          </div>
        )}

        <div className="flex items-center gap-2">
          {bulkActions.length > 0 && selectedItems.length > 0 && (
            <div className="flex items-center gap-2">
              <Badge variant="info">{selectedItems.length} seleccionados</Badge>
              {bulkActions.map((action, index) => (
                <Button
                  key={index}
                  variant={action.variant || 'secondary'}
                  size={action.size || 'sm'}
                  onClick={() => action.onClick(selectedItems)}
                  leftIcon={action.icon}
                >
                  {action.label}
                </Button>
              ))}
            </div>
          )}
        </div>
      </div>

      {/* Información de resultados */}
      <div className="flex items-center justify-between text-sm text-gray-500">
        <span>
          Mostrando {startItem} a {endItem} de {sortedData.length} resultados
        </span>
        {pagination && (
          <div className="flex items-center gap-2">
            <span>Mostrar:</span>
            <select
              value={itemsPerPageState}
              onChange={(e) => {
                setItemsPerPageState(Number(e.target.value));
                setCurrentPage(1);
              }}
              className="border border-gray-300 rounded-md px-2 py-1 text-sm"
            >
              {itemsPerPageOptions.map((option) => (
                <option key={option} value={option}>
                  {option}
                </option>
              ))}
            </select>
            <span>por página</span>
          </div>
        )}
      </div>

      {/* Tabla */}
      <div className="bg-white rounded-lg shadow overflow-hidden">
        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                {selectable && (
                  <th className="px-6 py-3 text-left">
                    <input
                      type="checkbox"
                      checked={selectedItems.length === paginatedData.length && paginatedData.length > 0}
                      onChange={(e) => handleSelectAll(e.target.checked)}
                      className="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                    />
                  </th>
                )}
                {columns.map((column) => (
                  <th
                    key={column.key}
                    className={cn(
                      'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider',
                      column.width && `w-${column.width}`,
                      column.align === 'center' && 'text-center',
                      column.align === 'right' && 'text-right',
                      sortable && column.sortable !== false && 'cursor-pointer hover:bg-gray-100'
                    )}
                    onClick={() => {
                      if (sortable && column.sortable !== false) {
                        handleSort(column.key);
                      }
                    }}
                  >
                    <div className={cn(
                      'flex items-center gap-1',
                      column.align === 'center' && 'justify-center',
                      column.align === 'right' && 'justify-end'
                    )}>
                      {column.header}
                      {sortable && column.sortable !== false && sortColumn === column.key && (
                        <svg
                          className={cn(
                            'w-4 h-4',
                            sortDirection === 'asc' ? 'rotate-180' : ''
                          )}
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 15l7-7 7 7" />
                        </svg>
                      )}
                    </div>
                  </th>
                ))}
                {actions.length > 0 && (
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Acciones
                  </th>
                )}
              </tr>
            </thead>
            <tbody className="bg-white divide-y divide-gray-200">
              {paginatedData.length === 0 ? (
                <tr>
                  <td
                    colSpan={columns.length + (selectable ? 1 : 0) + (actions.length > 0 ? 1 : 0)}
                    className="px-6 py-12 text-center"
                  >
                    <div className="flex flex-col items-center">
                      {emptyIcon || (
                        <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                      )}
                      <h3 className="mt-2 text-sm font-medium text-gray-900">{emptyMessage}</h3>
                      {searchTerm && (
                        <p className="mt-1 text-sm text-gray-500">
                          No se encontraron resultados para "{searchTerm}"
                        </p>
                      )}
                    </div>
                  </td>
                </tr>
              ) : (
                paginatedData.map((item, index) => (
                  <tr
                    key={item.id || index}
                    className={cn(
                      'hover:bg-gray-50 transition-colors duration-200',
                      onRowClick && 'cursor-pointer'
                    )}
                    onClick={() => onRowClick?.(item)}
                  >
                    {selectable && (
                      <td className="px-6 py-4 whitespace-nowrap">
                        <input
                          type="checkbox"
                          checked={selectedItems.some((selected) => selected.id === item.id)}
                          onChange={(e) => handleSelectItem(item, e.target.checked)}
                          onClick={(e) => e.stopPropagation()}
                          className="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                        />
                      </td>
                    )}
                    {columns.map((column) => (
                      <td
                        key={column.key}
                        className={cn(
                          'px-6 py-4 whitespace-nowrap text-sm text-gray-900',
                          column.align === 'center' && 'text-center',
                          column.align === 'right' && 'text-right'
                        )}
                      >
                        {column.accessor(item)}
                      </td>
                    ))}
                    {actions.length > 0 && (
                      <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div className="flex items-center justify-end space-x-2">
                          {actions.map((action, actionIndex) => {
                            if (action.hidden?.(item)) return null;
                            
                            return (
                              <Button
                                key={actionIndex}
                                variant={action.variant || 'ghost'}
                                size={action.size || 'sm'}
                                onClick={(e) => {
                                  e.stopPropagation();
                                  action.onClick(item);
                                }}
                                disabled={action.disabled?.(item)}
                                leftIcon={action.icon}
                              >
                                {action.label}
                              </Button>
                            );
                          })}
                        </div>
                      </td>
                    )}
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* Paginación */}
      {pagination && totalPages > 1 && (
        <div className="flex items-center justify-between">
          <div className="flex items-center gap-2 text-sm text-gray-700">
            <span>
              Página {currentPage} de {totalPages}
            </span>
          </div>
          <div className="flex items-center space-x-2">
            <Button
              variant="secondary"
              size="sm"
              onClick={() => setCurrentPage(currentPage - 1)}
              disabled={currentPage === 1}
            >
              Anterior
            </Button>
            
            {/* Números de página */}
            <div className="flex items-center space-x-1">
              {Array.from({ length: Math.min(5, totalPages) }, (_, i) => {
                let pageNumber;
                if (totalPages <= 5) {
                  pageNumber = i + 1;
                } else if (currentPage <= 3) {
                  pageNumber = i + 1;
                } else if (currentPage >= totalPages - 2) {
                  pageNumber = totalPages - 4 + i;
                } else {
                  pageNumber = currentPage - 2 + i;
                }

                return (
                  <Button
                    key={pageNumber}
                    variant={currentPage === pageNumber ? 'primary' : 'secondary'}
                    size="sm"
                    onClick={() => setCurrentPage(pageNumber)}
                    className="w-8 h-8 p-0"
                  >
                    {pageNumber}
                  </Button>
                );
              })}
            </div>

            <Button
              variant="secondary"
              size="sm"
              onClick={() => setCurrentPage(currentPage + 1)}
              disabled={currentPage === totalPages}
            >
              Siguiente
            </Button>
          </div>
        </div>
      )}
    </div>
  );
}; 