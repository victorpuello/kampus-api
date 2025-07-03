import React, { useState, useEffect } from 'react';
import { Link, useParams, useNavigate } from 'react-router-dom';
import { useAlertContext } from '../contexts/AlertContext';
import { useConfirm } from '../hooks/useConfirm';
import axiosClient from '../api/axiosClient';
import { 
  PageHeader, 
  Button, 
  DataTable,
  LoadingSpinner,
  Card,
  CardHeader,
  CardBody
} from '../components/ui';

interface Sede {
  id: number;
  nombre: string;
  direccion: string;
  telefono: string;
  created_at: string;
  updated_at: string;
}

interface Institution {
  id: number;
  nombre: string;
  siglas: string;
}

interface SedesResponse {
  data: Sede[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

const InstitutionSedesPage: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const { showSuccess, showError } = useAlertContext();
  const { confirm } = useConfirm();
  
  const [sedes, setSedes] = useState<Sede[]>([]);
  const [institution, setInstitution] = useState<Institution | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [totalItems, setTotalItems] = useState(0);
  const [selectedSedes, setSelectedSedes] = useState<Sede[]>([]);

  // Validar ID antes de hacer fetch
  const isValidId = (id: string | undefined): boolean => {
    if (!id) return false;
    const numId = parseInt(id);
    return !isNaN(numId) && numId > 0;
  };

  const fetchInstitution = async () => {
    if (!isValidId(id)) {
      setError('ID de institución inválido');
      return;
    }

    try {
      const response = await axiosClient.get(`/instituciones/${id}`);
      const institutionData = response.data.data || response.data;
      setInstitution(institutionData);
    } catch (err: any) {
      console.error('Error fetching institution:', err);
      setError('Error al cargar la institución');
    }
  };

  const fetchSedes = async (page = 1, search = '') => {
    if (!isValidId(id)) {
      setError('ID de institución inválido');
      return;
    }

    try {
      setLoading(true);
      setError(null);
      
      const params = new URLSearchParams({
        page: page.toString(),
        per_page: '10',
        ...(search && { search })
      });
      
      const response = await axiosClient.get(`/instituciones/${id}/sedes?${params}`);
      const data: SedesResponse = response.data;
      
      setSedes(data.data);
      setCurrentPage(data.current_page);
      setTotalPages(data.last_page);
      setTotalItems(data.total);
      
    } catch (err: any) {
      console.error('Error fetching sedes:', err);
      setError('Error al cargar las sedes');
      showError('Error al cargar las sedes');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchInstitution();
    fetchSedes();
  }, [id]);

  const handleSearch = (value: string) => {
    fetchSedes(1, value);
  };

  const handlePageChange = (page: number) => {
    fetchSedes(page);
  };

  const handleDelete = async (sede: Sede) => {
    const confirmed = await confirm({
      title: '¿Eliminar sede?',
      message: `¿Estás seguro de que quieres eliminar la sede "${sede.nombre}"? Esta acción no se puede deshacer.`,
      confirmText: 'Sí, eliminar',
      cancelText: 'Cancelar',
      variant: 'danger'
    });

    if (confirmed) {
      try {
        setLoading(true);
        await axiosClient.delete(`/sedes/${sede.id}`);
        showSuccess('Sede eliminada exitosamente');
        fetchSedes(currentPage);
      } catch (err: any) {
        console.error('Error deleting sede:', err);
        const errorMessage = err.response?.data?.message || 'Error al eliminar la sede';
        showError(errorMessage);
      } finally {
        setLoading(false);
      }
    }
  };

  const handleBulkDelete = async (selectedSedes: Sede[]) => {
    const confirmed = await confirm({
      title: '¿Eliminar sedes seleccionadas?',
      message: `¿Estás seguro de que quieres eliminar ${selectedSedes.length} sede(s)? Esta acción no se puede deshacer.`,
      confirmText: 'Sí, eliminar',
      cancelText: 'Cancelar',
      variant: 'danger'
    });

    if (confirmed) {
      try {
        setLoading(true);
        const deletePromises = selectedSedes.map(sede =>
          axiosClient.delete(`/sedes/${sede.id}`)
        );
        await Promise.all(deletePromises);
        showSuccess(`${selectedSedes.length} sede(s) eliminada(s) exitosamente`);
        setSelectedSedes([]);
        fetchSedes(currentPage);
      } catch (err: any) {
        console.error('Error bulk deleting sedes:', err);
        showError('Error al eliminar las sedes seleccionadas');
      } finally {
        setLoading(false);
      }
    }
  };

  const formatDate = (dateString: string) => {
    try {
      return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    } catch (error) {
      return 'Fecha inválida';
    }
  };

  const columns = [
    {
      key: 'id',
      header: 'ID',
      accessor: (sede: Sede) => (
        <span className="text-sm text-gray-900">{sede.id}</span>
      ),
      sortable: true
    },
    {
      key: 'nombre',
      header: 'Nombre',
      accessor: (sede: Sede) => (
        <div>
          <div className="text-sm font-medium text-gray-900">{sede.nombre}</div>
        </div>
      ),
      sortable: true
    },
    {
      key: 'direccion',
      header: 'Dirección',
      accessor: (sede: Sede) => (
        <div className="max-w-xs truncate" title={sede.direccion}>
          <span className="text-sm text-gray-900">{sede.direccion}</span>
        </div>
      ),
      sortable: true
    },
    {
      key: 'telefono',
      header: 'Teléfono',
      accessor: (sede: Sede) => (
        <span className="text-sm text-gray-900">{sede.telefono || '-'}</span>
      ),
      sortable: true
    },
    {
      key: 'created_at',
      header: 'Fecha de Creación',
      accessor: (sede: Sede) => (
        <span className="text-sm text-gray-500">{formatDate(sede.created_at)}</span>
      ),
      sortable: true
    }
  ];

  const actions = [
    {
      label: 'Ver',
      variant: 'primary' as const,
      size: 'sm' as const,
      onClick: (sede: Sede) => navigate(`/instituciones/${id}/sedes/${sede.id}`)
    },
    {
      label: 'Editar',
      variant: 'secondary' as const,
      size: 'sm' as const,
      onClick: (sede: Sede) => navigate(`/instituciones/${id}/sedes/${sede.id}/editar`)
    },
    {
      label: 'Eliminar',
      variant: 'danger' as const,
      size: 'sm' as const,
      onClick: handleDelete
    }
  ];

  const bulkActions = [
    {
      label: 'Eliminar Seleccionadas',
      variant: 'danger' as const,
      onClick: () => handleBulkDelete(selectedSedes),
      disabled: () => selectedSedes.length === 0
    }
  ];

  if (loading && sedes.length === 0) {
    return (
      <div className="space-y-6">
        <PageHeader
          title="Sedes de la Institución"
          description="Gestiona las sedes de la institución seleccionada"
        >
          <Link to={`/instituciones/${id}/sedes/crear`}>
            <Button variant="primary">
              <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              Nueva Sede
            </Button>
          </Link>
        </PageHeader>
        <div className="flex items-center justify-center py-12">
          <LoadingSpinner text="Cargando sedes..." />
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <PageHeader
        title={`Sedes de ${institution ? institution.nombre : '...'}`}
        description="Gestiona las sedes de la institución seleccionada"
      >
        <div className="flex space-x-3">
          <Link to={`/instituciones/${id}/sedes/crear`}>
            <Button variant="primary">
              <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              Nueva Sede
            </Button>
          </Link>
          <Button variant="secondary" onClick={() => navigate('/instituciones')}>
            Volver a Instituciones
          </Button>
        </div>
      </PageHeader>

      <DataTable
        data={sedes}
        columns={columns}
        actions={actions}
        loading={loading}
        error={error}
        searchable={true}
        searchPlaceholder="Buscar sedes por nombre o dirección..."
        searchKeys={['nombre', 'direccion']}
        sortable={true}
        pagination={true}
        itemsPerPage={10}
        itemsPerPageOptions={[5, 10, 25, 50]}
        emptyMessage="No hay sedes registradas para esta institución"
        onRowClick={(sede) => navigate(`/instituciones/${id}/sedes/${sede.id}`)}
        selectable={true}
        onSelectionChange={setSelectedSedes}
        bulkActions={bulkActions}
      />
    </div>
  );
};

export default InstitutionSedesPage; 