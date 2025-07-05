import { useEffect, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import axiosClient from '../api/axiosClient';
import { Button } from '../components/ui/Button';
import { DataTable } from '../components/ui/DataTable';
import type { Column, ActionButton } from '../components/ui/DataTable';
import { useAlertContext } from '../contexts/AlertContext';
import { useConfirm } from '../hooks/useConfirm';
import ConfirmDialog from '../components/ui/ConfirmDialog';

interface Grado {
  id: number;
  nombre: string;
  descripcion?: string;
  nivel?: string;
  estado: string;
  grupos_count?: number;
}

interface GradosResponse {
  data: Grado[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number;
  to: number;
}

const GradesListPage = () => {
  const navigate = useNavigate();
  const { showSuccess, showError } = useAlertContext();
  const { dialogState, confirm, setLoading: setConfirmLoading } = useConfirm();
  const [grados, setGrados] = useState<Grado[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  
  // Estados para paginación del servidor
  const [currentPage, setCurrentPage] = useState(1);
  const [itemsPerPage, setItemsPerPage] = useState(10);
  const [searchTerm, setSearchTerm] = useState('');
  const [totalItems, setTotalItems] = useState(0);
  const [totalPages, setTotalPages] = useState(0);

  const fetchGrados = async (page = 1, perPage = 10, search = '') => {
    try {
      setLoading(true);
      const params = new URLSearchParams({
        page: page.toString(),
        per_page: perPage.toString(),
      });
      
      if (search) {
        params.append('search', search);
      }
      
      const response = await axiosClient.get(`/grados?${params.toString()}`);
      const data: GradosResponse = response.data;
      
      console.log('Datos de grados recibidos:', data);
      setGrados(data.data);
      setTotalItems(data.total);
      setTotalPages(data.last_page);
      setCurrentPage(data.current_page);
      setItemsPerPage(data.per_page);
      setError(null);
    } catch (err: any) {
      setError(err.response?.data?.message || 'Error al cargar los grados');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchGrados(currentPage, itemsPerPage, searchTerm);
  }, [currentPage, itemsPerPage, searchTerm]);

  // Manejar cambio de página
  const handlePageChange = (page: number) => {
    setCurrentPage(page);
  };

  // Manejar cambio de elementos por página
  const handleItemsPerPageChange = (perPage: number) => {
    setItemsPerPage(perPage);
    setCurrentPage(1); // Resetear a la primera página
  };

  // Manejar búsqueda
  const handleSearch = (search: string) => {
    setSearchTerm(search);
    setCurrentPage(1); // Resetear a la primera página
  };

  const handleDelete = async (grado: Grado) => {
    const confirmed = await confirm({
      title: 'Eliminar Grado',
      message: `¿Estás seguro de que deseas eliminar el grado "${grado.nombre}"? Esta acción no se puede deshacer.`,
      confirmText: 'Eliminar',
      cancelText: 'Cancelar',
      variant: 'danger'
    });

    if (!confirmed) return;

    try {
      setConfirmLoading(true);
      await axiosClient.delete(`/grados/${grado.id}`);
      showSuccess('Grado eliminado exitosamente', 'Éxito');
      fetchGrados(currentPage, itemsPerPage, searchTerm);
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || 'Error al eliminar el grado';
      showError(errorMessage, 'Error');
      setError(errorMessage);
    } finally {
      setConfirmLoading(false);
    }
  };

  const handleBulkDelete = async (selectedGrados: Grado[]) => {
    const confirmed = await confirm({
      title: 'Eliminar Grados',
      message: `¿Estás seguro de que deseas eliminar ${selectedGrados.length} grados? Esta acción no se puede deshacer.`,
      confirmText: 'Eliminar',
      cancelText: 'Cancelar',
      variant: 'danger'
    });

    if (!confirmed) return;

    try {
      setConfirmLoading(true);
      await Promise.all(selectedGrados.map(grado => 
        axiosClient.delete(`/grados/${grado.id}`)
      ));
      showSuccess(`${selectedGrados.length} grados eliminados exitosamente`, 'Éxito');
      fetchGrados(currentPage, itemsPerPage, searchTerm);
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || 'Error al eliminar los grados';
      showError(errorMessage, 'Error');
      setError(errorMessage);
    } finally {
      setConfirmLoading(false);
    }
  };

  // Definir las columnas de la tabla
  const columns: Column<Grado>[] = [
    {
      key: 'nombre',
      header: 'Grado',
      accessor: (grado) => (
        <div className="flex items-center">
          <div className="flex-shrink-0 h-10 w-10">
            <div className="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
              <span className="text-sm font-medium text-green-700">
                {grado.nombre.charAt(0)}
              </span>
            </div>
          </div>
          <div className="ml-4">
            <div className="text-sm font-medium text-gray-900">
              {grado.nombre}
            </div>
            {grado.nivel && (
              <div className="text-sm text-gray-500">
                {grado.nivel}
              </div>
            )}
          </div>
        </div>
      ),
      sortable: true,
    },
    {
      key: 'descripcion',
      header: 'Descripción',
      accessor: (grado) => (
        <span className="text-sm text-gray-500">
          {grado.descripcion || 'Sin descripción'}
        </span>
      ),
      sortable: true,
    },
    {
      key: 'grupos_count',
      header: 'Grupos',
      accessor: (grado) => (
        <span className="text-sm text-gray-500">
          {grado.grupos_count || 0} grupos
        </span>
      ),
      sortable: true,
      align: 'center',
    },
    {
      key: 'estado',
      header: 'Estado',
      accessor: (grado) => (
        <span
          className={`inline-flex rounded-full px-2 text-xs font-semibold leading-5 ${
            grado.estado === 'activo'
              ? 'bg-green-100 text-green-800'
              : 'bg-red-100 text-red-800'
          }`}
        >
          {grado.estado}
        </span>
      ),
      sortable: true,
      align: 'center',
    },
  ];

  // Definir las acciones de la tabla
  const actions: ActionButton<Grado>[] = [
    {
      label: 'Ver',
      variant: 'ghost',
      size: 'sm',
      onClick: (grado) => navigate(`/grados/${grado.id}`),
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
      onClick: (grado) => navigate(`/grados/${grado.id}/editar`),
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
  const bulkActions: ActionButton<Grado[]>[] = [
    {
      label: 'Eliminar Seleccionados',
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
          <h1 className="text-2xl font-bold text-gray-900">Grados</h1>
          <p className="text-gray-600 mt-1">
            Gestiona todos los grados académicos del sistema
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
          <Link to="/grados/crear">Agregar Grado</Link>
        </Button>
      </div>

      {/* DataTable */}
      <DataTable
        data={grados}
        columns={columns}
        actions={actions}
        loading={loading}
        error={error}
        searchable={true}
        searchPlaceholder="Buscar grados por nombre, nivel o descripción..."
        searchKeys={['nombre', 'nivel', 'descripcion', 'estado']}
        sortable={true}
        pagination={true}
        itemsPerPage={itemsPerPage}
        itemsPerPageOptions={[5, 10, 25, 50]}
        emptyMessage="No hay grados registrados"
        emptyIcon={
          <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
          </svg>
        }
        selectable={true}
        bulkActions={bulkActions}
        onRowClick={(grado) => navigate(`/grados/${grado.id}`)}
        // Props para paginación del servidor
        serverSidePagination={true}
        currentPage={currentPage}
        totalPages={totalPages}
        totalItems={totalItems}
        onPageChange={handlePageChange}
        onItemsPerPageChange={handleItemsPerPageChange}
        onSearch={handleSearch}
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

export default GradesListPage; 