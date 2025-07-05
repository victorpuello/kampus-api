import { useEffect, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import axiosClient from '../api/axiosClient';
import { Button } from '../components/ui/Button';
import { DataTable } from '../components/ui/DataTable';
import type { Column, ActionButton } from '../components/ui/DataTable';
import { useAlertContext } from '../contexts/AlertContext';
import { useConfirm } from '../hooks/useConfirm';
import ConfirmDialog from '../components/ui/ConfirmDialog';

interface Anio {
  id: number;
  nombre: string;
  fecha_inicio: string;
  fecha_fin: string;
  institucion_id: number;
  estado: string;
  institucion?: {
    id: number;
    nombre: string;
    siglas: string;
  };
}

interface AniosResponse {
  data: Anio[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

const AcademicYearsListPage = () => {
  const navigate = useNavigate();
  const { showSuccess, showError } = useAlertContext();
  const { dialogState, confirm, setLoading: setConfirmLoading } = useConfirm();
  const [anios, setAnios] = useState<Anio[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [currentPage, setCurrentPage] = useState(1);
  const [itemsPerPage, setItemsPerPage] = useState(10);
  const [searchTerm, setSearchTerm] = useState('');
  const [totalItems, setTotalItems] = useState(0);
  const [totalPages, setTotalPages] = useState(0);

  const fetchAnios = async (page = 1, perPage = 10, search = '') => {
    try {
      setLoading(true);
      const params = new URLSearchParams({
        page: page.toString(),
        per_page: perPage.toString(),
      });
      if (search) {
        params.append('search', search);
      }
      const response = await axiosClient.get(`/anios?${params.toString()}`);
      const data: AniosResponse = response.data;
      setAnios(data.data);
      setTotalItems(data.total);
      setTotalPages(data.last_page);
      setCurrentPage(data.current_page);
      setItemsPerPage(data.per_page);
      setError(null);
    } catch (err: any) {
      setError(err.response?.data?.message || 'Error al cargar los años académicos');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchAnios(currentPage, itemsPerPage, searchTerm);
  }, [currentPage, itemsPerPage, searchTerm]);

  const handlePageChange = (page: number) => setCurrentPage(page);
  const handleItemsPerPageChange = (perPage: number) => {
    setItemsPerPage(perPage);
    setCurrentPage(1);
  };
  const handleSearch = (search: string) => {
    setSearchTerm(search);
    setCurrentPage(1);
  };

  const handleDelete = async (anio: Anio) => {
    const confirmed = await confirm({
      title: 'Eliminar Año Académico',
      message: `¿Estás seguro de que deseas eliminar el año académico "${anio.nombre}"? Esta acción no se puede deshacer.`,
      confirmText: 'Eliminar',
      cancelText: 'Cancelar',
      variant: 'danger',
    });
    if (!confirmed) return;
    try {
      setConfirmLoading(true);
      await axiosClient.delete(`/anios/${anio.id}`);
      showSuccess('Año académico eliminado exitosamente', 'Éxito');
      fetchAnios(currentPage, itemsPerPage, searchTerm);
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || 'Error al eliminar el año académico';
      showError(errorMessage, 'Error');
      setError(errorMessage);
    } finally {
      setConfirmLoading(false);
    }
  };

  const columns: Column<Anio>[] = [
    {
      key: 'nombre',
      header: 'Año Académico',
      accessor: (anio) => (
        <div className="flex flex-col">
          <span className="font-medium text-gray-900">{anio.nombre}</span>
          <span className="text-xs text-gray-500">{anio.fecha_inicio} a {anio.fecha_fin}</span>
        </div>
      ),
      sortable: true,
    },
    {
      key: 'institucion',
      header: 'Institución',
      accessor: (anio) => (
        <div className="flex flex-col">
          {anio.institucion ? (
            <>
              <span className="font-medium text-gray-900">{anio.institucion.nombre}</span>
              <span className="text-xs text-gray-500">({anio.institucion.siglas})</span>
            </>
          ) : (
            <span className="text-gray-500">No asignada</span>
          )}
        </div>
      ),
      sortable: true,
    },
    {
      key: 'estado',
      header: 'Estado',
      accessor: (anio) => (
        <span className={`inline-flex rounded-full px-2 text-xs font-semibold leading-5 ${anio.estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>{anio.estado}</span>
      ),
      sortable: true,
      align: 'center',
    },
  ];

  const actions: ActionButton<Anio>[] = [
    {
      label: 'Ver',
      variant: 'ghost',
      size: 'sm',
      onClick: (anio) => navigate(`/anios/${anio.id}`),
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
      onClick: (anio) => navigate(`/anios/${anio.id}/editar`),
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

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Años Académicos</h1>
          <p className="text-gray-600 mt-1">Gestiona los años académicos del sistema</p>
        </div>
        <Button
          asChild
          leftIcon={
            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
          }
        >
          <Link to="/anios/crear">Agregar Año</Link>
        </Button>
      </div>
      <DataTable
        data={anios}
        columns={columns}
        actions={actions}
        loading={loading}
        error={error}
        searchable={true}
        searchPlaceholder="Buscar años por nombre..."
        searchKeys={['nombre', 'estado', 'institucion.nombre', 'institucion.siglas']}
        sortable={true}
        pagination={true}
        itemsPerPage={itemsPerPage}
        itemsPerPageOptions={[5, 10, 25, 50]}
        emptyMessage="No hay años académicos registrados"
        emptyIcon={
          <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
          </svg>
        }
        serverSidePagination={true}
        currentPage={currentPage}
        totalPages={totalPages}
        totalItems={totalItems}
        onPageChange={handlePageChange}
        onItemsPerPageChange={handleItemsPerPageChange}
        onSearch={handleSearch}
      />
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

export default AcademicYearsListPage; 