import { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import axiosClient from '../api/axiosClient';
import { Button } from '../components/ui/Button';
import { DataTable } from '../components/ui/DataTable';
import type { Column, ActionButton } from '../components/ui/DataTable';
import { useAlertContext } from '../contexts/AlertContext';
import { useConfirm } from '../hooks/useConfirm';
import PageHeader from '../components/ui/PageHeader';

interface Anio {
  id: number;
  nombre: string;
  fecha_inicio: string;
  fecha_fin: string;
  estado: string;
  institucion?: {
    id: number;
    nombre: string;
    siglas: string;
  };
}

interface Periodo {
  id: number;
  nombre: string;
  fecha_inicio: string;
  fecha_fin: string;
  anio_id: number;
}

interface PeriodosResponse {
  data: Periodo[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

const AnioPeriodosListPage = () => {
  const { anioId } = useParams<{ anioId: string }>();
  const navigate = useNavigate();
  const { showSuccess, showError } = useAlertContext();
  const { confirm } = useConfirm();
  
  const [anio, setAnio] = useState<Anio | null>(null);
  const [periodos, setPeriodos] = useState<Periodo[]>([]);
  const [loading, setLoading] = useState(true);
  const [loadingPeriodos, setLoadingPeriodos] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [totalItems, setTotalItems] = useState(0);
  const [searchTerm, setSearchTerm] = useState('');

  useEffect(() => {
    const fetchAnio = async () => {
      try {
        setLoading(true);
        const response = await axiosClient.get(`/anios/${anioId}`);
        setAnio(response.data.data || response.data);
        setError(null);
      } catch (err: any) {
        setError(err.response?.data?.message || 'Error al cargar el año académico');
      } finally {
        setLoading(false);
      }
    };
    if (anioId) {
      fetchAnio();
    }
  }, [anioId]);

  useEffect(() => {
    if (anioId) {
      fetchPeriodos();
    }
  }, [anioId, currentPage, searchTerm]);

  const fetchPeriodos = async () => {
    try {
      setLoadingPeriodos(true);
      const params = new URLSearchParams({
        page: currentPage.toString(),
        per_page: '10',
        ...(searchTerm && { search: searchTerm }),
      });

      const response = await axiosClient.get(`/anios/${anioId}/periodos?${params}`);
      const data: PeriodosResponse = response.data;
      
      setPeriodos(data.data);
      setTotalPages(data.last_page);
      setTotalItems(data.total);
    } catch (err: any) {
      showError('Error al cargar los periodos');
    } finally {
      setLoadingPeriodos(false);
    }
  };

  const handleDelete = async (periodo: Periodo) => {
    const confirmed = await confirm({
      title: 'Eliminar Periodo',
      message: `¿Estás seguro de que quieres eliminar el periodo "${periodo.nombre}"?`,
      variant: 'danger',
    });

    if (confirmed) {
      try {
        await axiosClient.delete(`/anios/${anioId}/periodos/${periodo.id}`);
        showSuccess('Periodo eliminado exitosamente');
        fetchPeriodos();
      } catch (err: any) {
        showError('Error al eliminar el periodo');
      }
    }
  };

  const handleEdit = (periodo: Periodo) => {
    navigate(`/anios/${anioId}/periodos/${periodo.id}/editar`);
  };

  const handleView = (periodo: Periodo) => {
    navigate(`/anios/${anioId}/periodos/${periodo.id}`);
  };

  const handlePageChange = (page: number) => setCurrentPage(page);
  const handleSearch = (search: string) => {
    setSearchTerm(search);
    setCurrentPage(1);
  };

  const columns: Column<Periodo>[] = [
    {
      key: 'nombre',
      header: 'Nombre',
      accessor: (periodo) => periodo.nombre,
      sortable: true,
    },
    {
      key: 'fecha_inicio',
      header: 'Fecha Inicio',
      accessor: (periodo) => new Date(periodo.fecha_inicio).toLocaleDateString('es-ES'),
      sortable: true,
    },
    {
      key: 'fecha_fin',
      header: 'Fecha Fin',
      accessor: (periodo) => new Date(periodo.fecha_fin).toLocaleDateString('es-ES'),
      sortable: true,
    },
    {
      key: 'duracion',
      header: 'Duración',
      accessor: (periodo) => {
        const inicio = new Date(periodo.fecha_inicio);
        const fin = new Date(periodo.fecha_fin);
        const diffTime = Math.abs(fin.getTime() - inicio.getTime());
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        return `${diffDays} días`;
      },
    },
  ];

  const actions: ActionButton<Periodo>[] = [
    {
      label: 'Ver',
      variant: 'primary',
      size: 'sm',
      onClick: handleView,
    },
    {
      label: 'Editar',
      variant: 'secondary',
      size: 'sm',
      onClick: handleEdit,
    },
    {
      label: 'Eliminar',
      variant: 'danger',
      size: 'sm',
      onClick: handleDelete,
    },
  ];

  if (loading) {
    return (
      <div className="flex justify-center items-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900"></div>
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

  if (!anio) return null;

  return (
    <div className="space-y-6">
      <PageHeader
        title={`Periodos - ${anio.nombre}`}
        description={`Gestiona los periodos académicos del año ${anio.nombre}`}
      >
        <div className="flex space-x-3">
          <Button
            variant="primary"
            onClick={() => navigate(`/anios/${anioId}/periodos/crear`)}
          >
            Crear Periodo
          </Button>
          <Button
            variant="secondary"
            onClick={() => navigate(`/anios/${anioId}`)}
          >
            Volver al Año
          </Button>
        </div>
      </PageHeader>

      <DataTable
        data={periodos}
        columns={columns}
        actions={actions}
        loading={loadingPeriodos}
        searchable={true}
        searchPlaceholder="Buscar periodos..."
        searchKeys={['nombre']}
        pagination={true}
        serverSidePagination={true}
        currentPage={currentPage}
        totalPages={totalPages}
        totalItems={totalItems}
        onPageChange={handlePageChange}
        onSearch={handleSearch}
        emptyMessage="No hay periodos registrados para este año académico"
        onRowClick={handleView}
      />
    </div>
  );
};

export default AnioPeriodosListPage; 