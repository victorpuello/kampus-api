import { Link, useNavigate } from 'react-router-dom';
import { Button } from '../components/ui/Button';
import { ServerDataTable } from '../components/ui/ServerDataTable';
import type { Column, ActionButton } from '../components/ui/DataTable';
import { useAlertContext } from '../contexts/AlertContext';
import { useConfirm } from '../hooks/useConfirm';
import { useServerPagination } from '../hooks/useServerPagination';
import ConfirmDialog from '../components/ui/ConfirmDialog';
import axiosClient from '../api/axiosClient';


interface Area {
  id: number;
  nombre: string;
  descripcion?: string;
  color?: string;
  asignaturas_count?: number;
}

const AreasListPage = () => {
  const navigate = useNavigate();
  const { showSuccess, showError } = useAlertContext();
  const { dialogState, confirm, setLoading: setConfirmLoading } = useConfirm();
  
  const {
    data: areas,
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
  } = useServerPagination<Area>({
    endpoint: 'areas',
    searchKeys: ['nombre', 'codigo', 'descripcion'],
  });

  const handleDelete = async (area: Area) => {
    const confirmed = await confirm({
      title: 'Eliminar Área',
      message: `¿Estás seguro de que deseas eliminar el área "${area.nombre}"? Esta acción no se puede deshacer.`,
      confirmText: 'Eliminar',
      cancelText: 'Cancelar',
      variant: 'danger'
    });

    if (!confirmed) return;

    try {
      setConfirmLoading(true);
      await axiosClient.delete(`/areas/${area.id}`);
      showSuccess('Área eliminada exitosamente', 'Éxito');
      refreshData();
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || 'Error al eliminar el área';
      showError(errorMessage, 'Error');
    } finally {
      setConfirmLoading(false);
    }
  };

  const handleBulkDelete = async (selectedAreas: Area[]) => {
    const confirmed = await confirm({
      title: 'Eliminar Áreas',
      message: `¿Estás seguro de que deseas eliminar ${selectedAreas.length} áreas? Esta acción no se puede deshacer.`,
      confirmText: 'Eliminar',
      cancelText: 'Cancelar',
      variant: 'danger'
    });

    if (!confirmed) return;

    try {
      setConfirmLoading(true);
      await Promise.all(selectedAreas.map(area => 
        axiosClient.delete(`/areas/${area.id}`)
      ));
      showSuccess(`${selectedAreas.length} áreas eliminadas exitosamente`, 'Éxito');
      refreshData();
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || 'Error al eliminar las áreas';
      showError(errorMessage, 'Error');
    } finally {
      setConfirmLoading(false);
    }
  };

  // Definir las columnas de la tabla
  const columns: Column<Area>[] = [
    {
      key: 'nombre',
      header: 'Área',
      accessor: (area) => (
        <div className="flex items-center">
          <div className="flex-shrink-0 h-10 w-10">
            <div 
              className="h-10 w-10 rounded-full flex items-center justify-center"
              style={{ backgroundColor: area.color || '#3B82F6' }}
            >
              <span className="text-sm font-medium text-white">
                {area.nombre.charAt(0)}
              </span>
            </div>
          </div>
          <div className="ml-4">
            <div className="text-sm font-medium text-gray-900">
              {area.nombre}
            </div>
          </div>
        </div>
      ),
      sortable: true,
    },
    {
      key: 'descripcion',
      header: 'Descripción',
      accessor: (area) => (
        <span className="text-sm text-gray-500">
          {area.descripcion || 'Sin descripción'}
        </span>
      ),
      sortable: true,
    },
    {
      key: 'asignaturas_count',
      header: 'Asignaturas',
      accessor: (area) => (
        <span className="text-sm text-gray-500">
          {area.asignaturas_count || 0} asignaturas
        </span>
      ),
      sortable: true,
      align: 'center',
    },
  ];

  // Definir las acciones de la tabla
  const actions: ActionButton<Area>[] = [
    {
      label: 'Ver',
      variant: 'ghost',
      size: 'sm',
      onClick: (area) => navigate(`/areas/${area.id}`),
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
      onClick: (area) => navigate(`/areas/${area.id}/editar`),
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
  const bulkActions: ActionButton<Area[]>[] = [
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
          <h1 className="text-2xl font-bold text-gray-900">Áreas</h1>
          <p className="text-gray-600 mt-1">
            Gestiona todas las áreas académicas del sistema
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
          <Link to="/areas/crear">Agregar Área</Link>
        </Button>
      </div>

      {/* DataTable */}
      <ServerDataTable
        data={areas}
        columns={columns}
        actions={actions}
        loading={loading}
        error={error}
        searchable={true}
        searchPlaceholder="Buscar áreas por nombre o descripción..."
        searchKeys={['nombre', 'descripcion']}
        sortable={true}
        pagination={true}
        itemsPerPage={itemsPerPage}
        itemsPerPageOptions={[5, 10, 25, 50]}
        emptyMessage="No hay áreas registradas"
        emptyIcon={
          <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
          </svg>
        }
        selectable={true}
        bulkActions={bulkActions}
        onRowClick={(area) => navigate(`/areas/${area.id}`)}
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

export default AreasListPage; 