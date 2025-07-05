import { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import axiosClient from '../api/axiosClient';
import { Button } from '../components/ui/Button';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
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

interface Periodo {
  id: number;
  nombre: string;
  fecha_inicio: string;
  fecha_fin: string;
  anio_id: number;
  anio?: {
    id: number;
    nombre: string;
    estado: string;
  };
}

interface PeriodosResponse {
  data: Periodo[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

const AcademicYearDetailPage = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const { showSuccess, showError } = useAlertContext();
  const { confirm } = useConfirm();
  
  const [anio, setAnio] = useState<Anio | null>(null);
  const [periodos, setPeriodos] = useState<Periodo[]>([]);
  const [loading, setLoading] = useState(true);
  const [loadingPeriodos, setLoadingPeriodos] = useState(false);
  const [error, setError] = useState<string | null>(null);
  


  // Estados para paginación de periodos
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [totalItems, setTotalItems] = useState(0);
  const [searchTerm, setSearchTerm] = useState('');

  useEffect(() => {
    const fetchAnio = async () => {
      try {
        setLoading(true);
        const response = await axiosClient.get(`/anios/${id}`);
        setAnio(response.data.data || response.data);
        setError(null);
      } catch (err: any) {
        setError(err.response?.data?.message || 'Error al cargar el año académico');
      } finally {
        setLoading(false);
      }
    };
    if (id) {
      fetchAnio();
    }
  }, [id]);

  useEffect(() => {
    if (id) {
      fetchPeriodos();
    }
  }, [id, currentPage, searchTerm]);

  const fetchPeriodos = async () => {
    try {
      setLoadingPeriodos(true);
      const params = new URLSearchParams({
        page: currentPage.toString(),
        per_page: '10',
        ...(searchTerm && { search: searchTerm }),
      });

      const response = await axiosClient.get(`/anios/${id}/periodos?${params}`);
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



  const handleDeletePeriodo = async (periodo: Periodo) => {
    const confirmed = await confirm({
      title: 'Eliminar Periodo',
      message: `¿Estás seguro de que quieres eliminar el periodo "${periodo.nombre}"?`,
      variant: 'danger',
    });

    if (confirmed) {
      try {
        await axiosClient.delete(`/anios/${id}/periodos/${periodo.id}`);
        showSuccess('Periodo eliminado exitosamente');
        fetchPeriodos();
      } catch (err: any) {
        showError('Error al eliminar el periodo');
      }
    }
  };

  const handleEditPeriodo = (periodo: Periodo) => {
    navigate(`/anios/${id}/periodos/${periodo.id}/editar`);
  };

  const handleViewPeriodo = (periodo: Periodo) => {
    navigate(`/anios/${id}/periodos/${periodo.id}`);
  };

  const handlePageChange = (page: number) => setCurrentPage(page);
  const handleSearch = (search: string) => {
    setSearchTerm(search);
    setCurrentPage(1);
  };

  // Columnas para la tabla de periodos
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

  // Acciones para la tabla de periodos
  const actions: ActionButton<Periodo>[] = [
    {
      label: 'Ver',
      variant: 'primary',
      size: 'sm',
      onClick: handleViewPeriodo,
    },
    {
      label: 'Editar',
      variant: 'secondary',
      size: 'sm',
      onClick: handleEditPeriodo,
    },
    {
      label: 'Eliminar',
      variant: 'danger',
      size: 'sm',
      onClick: handleDeletePeriodo,
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
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">{anio.nombre}</h1>
          <p className="text-gray-600 mt-1">Año Académico</p>
        </div>
        <div className="flex space-x-3">
          <Button 
            variant="primary" 
            onClick={() => navigate(`/anios/${anio.id}/periodos`)}
          >
            Ver Periodos
          </Button>
          <Button 
            variant="secondary" 
            onClick={() => navigate(`/anios/${anio.id}/editar`)}
          >
            Editar Año
          </Button>
          <Button variant="ghost" onClick={() => navigate('/anios')}>
            Volver a la lista
          </Button>
        </div>
      </div>
      
      {/* Información del Año Académico */}
      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold text-gray-900">Información del Año Académico</h2>
        </CardHeader>
        <CardBody>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div className="space-y-3">
              <div>
                <span className="font-medium text-gray-700">Nombre:</span>
                <p className="text-gray-900">{anio.nombre}</p>
              </div>
              <div>
                <span className="font-medium text-gray-700">Fecha de Inicio:</span>
                <p className="text-gray-900">{new Date(anio.fecha_inicio).toLocaleDateString('es-ES')}</p>
              </div>
              <div>
                <span className="font-medium text-gray-700">Fecha de Fin:</span>
                <p className="text-gray-900">{new Date(anio.fecha_fin).toLocaleDateString('es-ES')}</p>
              </div>
            </div>
            
            <div className="space-y-3">
              <div>
                <span className="font-medium text-gray-700">Estado:</span>
                <span className={`inline-flex rounded-full px-2 text-xs font-semibold leading-5 ${
                  anio.estado === 'activo' 
                    ? 'bg-green-100 text-green-800' 
                    : 'bg-red-100 text-red-800'
                }`}>
                  {anio.estado}
                </span>
              </div>
              {anio.institucion && (
                <div>
                  <span className="font-medium text-gray-700">Institución:</span>
                  <p className="text-gray-900">{anio.institucion.nombre}</p>
                  <p className="text-sm text-gray-500">({anio.institucion.siglas})</p>
                </div>
              )}
            </div>
          </div>
        </CardBody>
      </Card>

      {/* Tabla de Periodos */}
      <Card>
        <CardHeader>
          <div className="flex items-center justify-between">
            <h2 className="text-lg font-semibold text-gray-900">Periodos Académicos</h2>
            <div className="text-sm text-gray-500">
              {totalItems} periodo{totalItems !== 1 ? 's' : ''} en total
            </div>
          </div>
        </CardHeader>
        <CardBody>
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
            onRowClick={handleViewPeriodo}
          />
        </CardBody>
      </Card>



      {/* ConfirmDialog se maneja automáticamente por el hook useConfirm */}
    </div>
  );
};

export default AcademicYearDetailPage; 