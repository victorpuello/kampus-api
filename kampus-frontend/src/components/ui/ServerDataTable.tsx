import React from 'react';
import { DataTable } from './DataTable';
import type { Column, ActionButton } from './DataTable';

// Definir el tipo base para elementos con ID
interface BaseItem {
  id?: number | string;
}

interface ServerDataTableProps<T extends BaseItem> {
  data: T[];
  columns: Column<T>[];
  actions?: ActionButton<T>[];
  loading: boolean;
  error: string | null;
  searchable?: boolean;
  searchPlaceholder?: string;
  searchKeys?: string[];
  sortable?: boolean;
  pagination?: boolean;
  itemsPerPage: number;
  itemsPerPageOptions?: number[];
  emptyMessage?: string;
  emptyIcon?: React.ReactNode;
  selectable?: boolean;
  bulkActions?: ActionButton<T[]>[];
  onRowClick?: (item: T) => void;
  // Props para paginación del servidor
  serverSidePagination?: boolean;
  serverSideSorting?: boolean;
  currentPage: number;
  totalPages: number;
  totalItems: number;
  onPageChange: (page: number) => void;
  onItemsPerPageChange: (perPage: number) => void;
  onSearch: (search: string) => void;
  onSort: (columnKey: string, direction: 'asc' | 'desc') => void;
}

export function ServerDataTable<T extends BaseItem>({
  data,
  columns,
  actions,
  loading,
  error,
  searchable = true,
  searchPlaceholder = "Buscar...",
  searchKeys = [],
  sortable = true,
  pagination = true,
  itemsPerPage,
  itemsPerPageOptions = [5, 10, 25, 50],
  emptyMessage = "No hay datos disponibles",
  emptyIcon,
  selectable = false,
  bulkActions,
  onRowClick,
  serverSidePagination = true,
  serverSideSorting = true,
  currentPage,
  totalPages,
  totalItems,
  onPageChange,
  onItemsPerPageChange,
  onSearch,
  onSort,
}: ServerDataTableProps<T>) {
  return (
    <DataTable
      data={data}
      columns={columns}
      actions={actions}
      loading={loading}
      error={error}
      searchable={searchable}
      searchPlaceholder={searchPlaceholder}
      searchKeys={searchKeys}
      sortable={sortable}
      pagination={pagination}
      itemsPerPage={itemsPerPage}
      itemsPerPageOptions={itemsPerPageOptions}
      emptyMessage={emptyMessage}
      emptyIcon={emptyIcon}
      selectable={selectable}
      bulkActions={bulkActions}
      onRowClick={onRowClick}
      // Configuración automática para paginación del servidor
      serverSidePagination={serverSidePagination}
      serverSideSorting={serverSideSorting}
      currentPage={currentPage}
      totalPages={totalPages}
      totalItems={totalItems}
      onPageChange={onPageChange}
      onItemsPerPageChange={onItemsPerPageChange}
      onSearch={onSearch}
      onSort={onSort}
    />
  );
} 