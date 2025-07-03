import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAlertContext } from '../contexts/AlertContext';
import { useConfirm } from '../hooks/useConfirm';
import axiosClient from '../api/axiosClient';
import { 
  PageHeader, 
  Button, 
  DataTable,
  LoadingSpinner
} from '../components/ui';

interface Institution {
  id: number;
  nombre: string;
  siglas: string;
  escudo?: string;
  created_at: string;
  updated_at: string;
}

interface InstitutionsResponse {
  data: Institution[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

const API_BASE_URL = import.meta.env.VITE_API_BASE || 'http://kampus.test';

const InstitutionsListPage: React.FC = () => {
  const navigate = useNavigate();
  const { showSuccess, showError } = useAlertContext();
  const { confirm } = useConfirm();
  
  const [institutions, setInstitutions] = useState<Institution[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [totalItems, setTotalItems] = useState(0);
  const [searchTerm, setSearchTerm] = useState('');
  const [selectedInstitutions, setSelectedInstitutions] = useState<Institution[]>([]);

  const fetchInstitutions = async (page = 1, search = '') => {
    try {
      setLoading(true);
      setError(null);
      
      const params = new URLSearchParams({
        page: page.toString(),
        per_page: '10',
        ...(search && { search })
      });
      
      const response = await axiosClient.get(`/instituciones?${params}`);
      const data: InstitutionsResponse = response.data;
      
      setInstitutions(data.data);
      setCurrentPage(data.current_page);
      setTotalPages(data.last_page);
      setTotalItems(data.total);
      
    } catch (error: any) {
      console.error('Error fetching institutions:', error);
      setError('Error al cargar las instituciones');
      showError('Error al cargar las instituciones');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchInstitutions(currentPage, searchTerm);
  }, [currentPage, searchTerm]);

  const handleSearch = (value: string) => {
    setSearchTerm(value);
    setCurrentPage(1);
  };

  const handlePageChange = (page: number) => {
    setCurrentPage(page);
  };

  const handleDelete = async (institution: Institution) => {
    const confirmed = await confirm({
      title: '¿Eliminar institución?',
      message: `¿Estás seguro de que quieres eliminar la institución "${institution.nombre}"? Esta acción no se puede deshacer.`,
      confirmText: 'Sí, eliminar',
      cancelText: 'Cancelar',
      variant: 'danger'
    });

    if (confirmed) {
      try {
        setLoading(true);
        await axiosClient.delete(`/instituciones/${institution.id}`);
        showSuccess('Institución eliminada exitosamente');
        fetchInstitutions(currentPage, searchTerm);
      } catch (error: any) {
        console.error('Error deleting institution:', error);
        const errorMessage = error.response?.data?.message || 'Error al eliminar la institución';
        showError(errorMessage);
      } finally {
        setLoading(false);
      }
    }
  };

  const handleBulkDelete = async (selectedInstitutions: Institution[]) => {
    const confirmed = await confirm({
      title: '¿Eliminar instituciones seleccionadas?',
      message: `¿Estás seguro de que quieres eliminar ${selectedInstitutions.length} institución(es)? Esta acción no se puede deshacer.`,
      confirmText: 'Sí, eliminar',
      cancelText: 'Cancelar',
      variant: 'danger'
    });

    if (confirmed) {
      try {
        setLoading(true);
        const deletePromises = selectedInstitutions.map(institution =>
          axiosClient.delete(`/instituciones/${institution.id}`)
        );
        await Promise.all(deletePromises);
        showSuccess(`${selectedInstitutions.length} institución(es) eliminada(s) exitosamente`);
        setSelectedInstitutions([]);
        fetchInstitutions(currentPage, searchTerm);
      } catch (error: any) {
        console.error('Error bulk deleting institutions:', error);
        showError('Error al eliminar las instituciones seleccionadas');
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
      accessor: (institution: Institution) => (
        <span className="text-sm text-gray-900">{institution.id}</span>
      ),
      sortable: true
    },
    {
      key: 'nombre',
      header: 'Nombre',
      accessor: (institution: Institution) => (
        <div className="flex items-center space-x-3">
          {institution.escudo && (
            <img 
              src={institution.escudo.startsWith('http') ? institution.escudo : `${API_BASE_URL}/storage/${institution.escudo}`}
              alt={`Escudo de ${institution.nombre}`}
              className="w-8 h-8 object-contain rounded border"
            />
          )}
          <div>
            <div className="text-sm font-medium text-gray-900">{institution.nombre}</div>
          </div>
        </div>
      ),
      sortable: true
    },
    {
      key: 'siglas',
      header: 'Siglas',
      accessor: (institution: Institution) => (
        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
          {institution.siglas}
        </span>
      ),
      sortable: true
    },
    {
      key: 'created_at',
      header: 'Fecha de Creación',
      accessor: (institution: Institution) => (
        <span className="text-sm text-gray-500">{formatDate(institution.created_at)}</span>
      ),
      sortable: true
    },
    {
      key: 'updated_at',
      header: 'Última Actualización',
      accessor: (institution: Institution) => (
        <span className="text-sm text-gray-500">{formatDate(institution.updated_at)}</span>
      ),
      sortable: true
    }
  ];

  const actions = [
    {
      label: 'Ver',
      variant: 'primary' as const,
      size: 'sm' as const,
      onClick: (institution: Institution) => navigate(`/instituciones/${institution.id}`)
    },
    {
      label: 'Editar',
      variant: 'secondary' as const,
      size: 'sm' as const,
      onClick: (institution: Institution) => navigate(`/instituciones/${institution.id}/editar`)
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
      onClick: () => handleBulkDelete(selectedInstitutions),
      disabled: () => selectedInstitutions.length === 0
    }
  ];

  if (loading && institutions.length === 0) {
    return (
      <div className="space-y-6">
        <PageHeader
          title="Instituciones"
          description="Gestiona las instituciones educativas del sistema"
        >
          <Button variant="primary" onClick={() => navigate('/instituciones/crear')}>
            <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Nueva Institución
          </Button>
        </PageHeader>
        <div className="flex items-center justify-center py-12">
          <LoadingSpinner text="Cargando instituciones..." />
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <PageHeader
        title="Instituciones"
        description="Gestiona las instituciones educativas del sistema"
      >
        <Button variant="primary" onClick={() => navigate('/instituciones/crear')}>
          <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Nueva Institución
        </Button>
      </PageHeader>

      <DataTable
        data={institutions}
        columns={columns}
        actions={actions}
        loading={loading}
        error={error}
        searchable={true}
        searchPlaceholder="Buscar instituciones..."
        searchKeys={['nombre', 'siglas']}
        sortable={true}
        pagination={true}
        itemsPerPage={10}
        itemsPerPageOptions={[5, 10, 25, 50]}
        emptyMessage="No hay instituciones registradas"
        onRowClick={(institution) => navigate(`/instituciones/${institution.id}`)}
        selectable={true}
        onSelectionChange={setSelectedInstitutions}
        bulkActions={bulkActions}
      />
    </div>
  );
};

export default InstitutionsListPage; 