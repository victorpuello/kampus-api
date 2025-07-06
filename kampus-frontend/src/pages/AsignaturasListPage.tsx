import { useEffect, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import axiosClient from '../api/axiosClient';
import { Button } from '../components/ui/Button';
import { DataTable } from '../components/ui/DataTable';
import { ServerDataTable } from '../components/ui/ServerDataTable';
import type { Column, ActionButton } from '../components/ui/DataTable';
import { useAlertContext } from '../contexts/AlertContext';
import { useConfirm } from '../hooks/useConfirm';
import ConfirmDialog from '../components/ui/ConfirmDialog';
import { useServerPagination } from '../hooks/useServerPagination';

interface Asignatura {
  id: number;
  nombre: string;
  codigo?: string;
  descripcion?: string;
  porcentaje_area: number;
  area?: {
    id: number;
    nombre: string;
    codigo?: string;
    color?: string;
  };
}

const AsignaturasListPage = () => {
  const navigate = useNavigate();
  const { showSuccess, showError } = useAlertContext();
  const { dialogState, confirm, setLoading: setConfirmLoading } = useConfirm();
  const {
    data: asignaturas,
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
  } = useServerPagination<Asignatura>({
    endpoint: 'asignaturas',
    searchKeys: ['nombre', 'codigo', 'descripcion'],
  });

  const handleDelete = async (asignatura: Asignatura) => {
    const confirmed = await confirm({
      title: 'Eliminar Asignatura',
      message: `¿Estás seguro de que deseas eliminar la asignatura "${asignatura.nombre}"? Esta acción no se puede deshacer.`,
      confirmText: 'Eliminar',
      cancelText: 'Cancelar',
      variant: 'danger'
    });

    if (!confirmed) return;

    try {
      setConfirmLoading(true);
      await axiosClient.delete(`/asignaturas/${asignatura.id}`);
      showSuccess('Asignatura eliminada exitosamente', 'Éxito');
      refreshData();
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || 'Error al eliminar la asignatura';
      showError(errorMessage, 'Error');
    } finally {
      setConfirmLoading(false);
    }
  };

  const handleBulkDelete = async (selectedAsignaturas: Asignatura[]) => {
    const confirmed = await confirm({
      title: 'Eliminar Asignaturas',
      message: `¿Estás seguro de que deseas eliminar ${selectedAsignaturas.length} asignaturas? Esta acción no se puede deshacer.`,
      confirmText: 'Eliminar',
      cancelText: 'Cancelar',
      variant: 'danger'
    });

    if (!confirmed) return;

    try {
      setConfirmLoading(true);
      await Promise.all(selectedAsignaturas.map(asignatura => 
        axiosClient.delete(`/asignaturas/${asignatura.id}`)
      ));
      showSuccess(`${selectedAsignaturas.length} asignaturas eliminadas exitosamente`, 'Éxito');
      refreshData();
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || 'Error al eliminar las asignaturas';
      showError(errorMessage, 'Error');
    } finally {
      setConfirmLoading(false);
    }
  };

  // Definir las columnas de la tabla
  const columns: Column<Asignatura>[] = [
    {
      key: 'nombre',
      header: 'Asignatura',
      accessor: (asignatura) => (
        <div className="flex items-center">
          <div className="flex-shrink-0 h-10 w-10">
            <div 
              className="h-10 w-10 rounded-full flex items-center justify-center"
              style={{ backgroundColor: asignatura.area?.color || '#3B82F6' }}
            >
              <span className="text-sm font-medium text-white">
                {asignatura.nombre.charAt(0)}
              </span>
            </div>
          </div>
          <div className="ml-4">
            <div className="text-sm font-medium text-gray-900">
              {asignatura.nombre}
            </div>
            {asignatura.codigo && (
              <div className="text-sm text-gray-500">
                {asignatura.codigo}
              </div>
            )}
          </div>
        </div>
      ),
      sortable: true,
    },
    {
      key: 'area',
      header: 'Área',
      accessor: (asignatura) => (
        <div className="flex items-center">
          {asignatura.area ? (
            <>
              <div 
                className="w-3 h-3 rounded-full mr-2"
                style={{ backgroundColor: asignatura.area.color || '#3B82F6' }}
              ></div>
              <span className="text-sm text-gray-900">
                {asignatura.area.nombre}
              </span>
            </>
          ) : (
            <span className="text-sm text-gray-500">Sin área</span>
          )}
        </div>
      ),
      sortable: true,
    },
    {
      key: 'porcentaje_area',
      header: 'Porcentaje Área',
      accessor: (asignatura) => (
        <span className="text-sm text-gray-500">
          {asignatura.porcentaje_area}%
        </span>
      ),
      sortable: true,
      align: 'center',
    },
  ];

  // Definir las acciones de la tabla
  const actions: ActionButton<Asignatura>[] = [
    {
      label: 'Ver',
      variant: 'ghost',
      size: 'sm',
      onClick: (asignatura) => navigate(`/asignaturas/${asignatura.id}`),
      icon: (
        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>
      ),
    },
    {
      label: 'Editar',
      variant: 'ghost',
      size: 'sm',
      onClick: (asignatura) => navigate(`/asignaturas/${asignatura.id}/editar`),
      icon: (
        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        </svg>
      ),
    },
    {
      label: 'Eliminar',
      variant: 'ghost',
      size: 'sm',
      onClick: handleDelete,
      icon: (
        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
      ),
    },
  ];

  // Acciones en lote
  const bulkActions: ActionButton<Asignatura[]>[] = [
    {
      label: 'Eliminar Seleccionadas',
      variant: 'danger',
      size: 'sm',
      onClick: handleBulkDelete,
      icon: (
        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
      ),
    },
  ];

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Asignaturas</h1>
          <p className="text-gray-600 mt-1">
            Gestiona todas las asignaturas del sistema
          </p>
        </div>
        <Button
          asChild
          leftIcon={
            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
          }
        >
          <Link to="/asignaturas/crear">Agregar Asignatura</Link>
        </Button>
      </div>

      {/* ServerDataTable */}
      <ServerDataTable
        data={asignaturas}
        columns={columns}
        actions={actions}
        loading={loading}
        error={error}
        searchable={true}
        searchPlaceholder="Buscar asignaturas por nombre, código o descripción..."
        searchKeys={['nombre', 'codigo', 'descripcion', 'area.nombre']}
        sortable={true}
        pagination={true}
        itemsPerPage={itemsPerPage}
        itemsPerPageOptions={[5, 10, 25, 50]}
        emptyMessage="No hay asignaturas registradas"
        emptyIcon={
          <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
          </svg>
        }
        selectable={true}
        bulkActions={bulkActions}
        onRowClick={(asignatura: Asignatura) => navigate(`/asignaturas/${asignatura.id}`)}
        // Props automáticas para paginación del servidor
        serverSidePagination={true}
        serverSideSorting={true}
        currentPage={currentPage}
        totalPages={totalPages}
        totalItems={totalItems}
        onPageChange={handlePageChange}
        onItemsPerPageChange={handleItemsPerPageChange}
        onSearch={handleSearch}
        onSort={handleSort}
      />

      {/* ConfirmDialog */}
      <ConfirmDialog
        isOpen={dialogState.isOpen}
        title={dialogState.title || 'Confirmar acción'}
        message={dialogState.message}
        confirmText={dialogState.confirmText || 'Confirmar'}
        cancelText={dialogState.cancelText || 'Cancelar'}
        variant={dialogState.variant || 'danger'}
        onConfirm={dialogState.onConfirm}
        onCancel={dialogState.onCancel}
        loading={dialogState.loading}
      />
    </div>
  );
};

export default AsignaturasListPage; 