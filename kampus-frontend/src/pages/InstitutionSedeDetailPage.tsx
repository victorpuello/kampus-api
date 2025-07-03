import React, { useState, useEffect } from 'react';
import { useNavigate, useParams, Link } from 'react-router-dom';
import { useAlertContext } from '../contexts/AlertContext';
import { useConfirm } from '../hooks/useConfirm';
import axiosClient from '../api/axiosClient';
import { PageHeader, Button, Badge, Card, CardHeader, CardBody, LoadingSpinner } from '../components/ui';

interface Sede {
  id: number;
  nombre: string;
  direccion: string;
  telefono: string;
  institucion: {
    id: number;
    nombre: string;
    siglas: string;
  };
  created_at: string;
  updated_at: string;
}

const InstitutionSedeDetailPage: React.FC = () => {
  const navigate = useNavigate();
  const { institutionId, id } = useParams<{ institutionId: string; id: string }>();
  const { showSuccess, showError } = useAlertContext();
  const { confirm } = useConfirm();
  
  const [sede, setSede] = useState<Sede | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  // Validar IDs antes de hacer fetch
  const isValidId = (id: string | undefined): boolean => {
    if (!id) return false;
    const numId = parseInt(id);
    return !isNaN(numId) && numId > 0;
  };

  useEffect(() => {
    const fetchSede = async () => {
      if (!isValidId(id) || !isValidId(institutionId)) {
        setError('ID de sede o institución inválido');
        setLoading(false);
        return;
      }

      try {
        setLoading(true);
        setError(null);
        
        const response = await axiosClient.get(`/sedes/${id}`);
        const sedeData = response.data.data || response.data;
        
        if (!sedeData || !sedeData.id) {
          setError('La sede no fue encontrada');
          return;
        }
        
        setSede(sedeData);
        
      } catch (error: any) {
        console.error('Error fetching sede:', error);
        
        if (error.response?.status === 404) {
          setError('La sede no fue encontrada');
        } else if (error.response?.status === 401) {
          setError('No tienes permisos para ver esta sede');
        } else {
          setError('Error al cargar la sede. Inténtalo de nuevo.');
          showError('Error al cargar la sede');
        }
      } finally {
        setLoading(false);
      }
    };

    fetchSede();
  }, [id, institutionId, showError]);

  const handleDelete = async () => {
    if (!sede) {
      showError('No se puede eliminar una sede que no existe');
      return;
    }

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
        navigate(`/instituciones/${institutionId}/sedes`);
      } catch (error: any) {
        console.error('Error deleting sede:', error);
        const errorMessage = error.response?.data?.message || 'Error al eliminar la sede';
        showError(errorMessage);
      } finally {
        setLoading(false);
      }
    }
  };

  const handleEdit = () => {
    if (!sede) {
      showError('No se puede editar una sede que no existe');
      return;
    }
    navigate(`/instituciones/${institutionId}/sedes/${sede.id}/editar`);
  };

  const formatDate = (dateString: string) => {
    try {
      return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      });
    } catch (error) {
      return 'Fecha inválida';
    }
  };

  // Estado de carga
  if (loading) {
    return (
      <div className="space-y-6">
        <PageHeader
          title="Detalles de la Sede"
          description="Información completa de la sede"
        />
        <div className="flex items-center justify-center py-12">
          <LoadingSpinner text="Cargando sede..." />
        </div>
      </div>
    );
  }

  // Estado de error
  if (error || !sede) {
    return (
      <div className="space-y-6">
        <PageHeader
          title="Detalles de la Sede"
          description="Información completa de la sede"
        />
        <Card>
          <CardBody>
            <div className="text-center py-8">
              <svg className="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
              </svg>
              <h3 className="mt-2 text-sm font-medium text-gray-900">Error al cargar la sede</h3>
              <p className="mt-1 text-sm text-gray-500">{error || 'La sede no fue encontrada'}</p>
              <div className="mt-6 flex justify-center space-x-3">
                <Button
                  variant="primary"
                  onClick={() => navigate(`/instituciones/${institutionId}/sedes`)}
                >
                  Volver a las sedes
                </Button>
                <Button
                  variant="secondary"
                  onClick={() => window.location.reload()}
                >
                  Reintentar
                </Button>
              </div>
            </div>
          </CardBody>
        </Card>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <PageHeader
        title="Detalles de la Sede"
        description="Información completa de la sede"
      >
        <div className="flex space-x-3">
          <Button variant="primary" onClick={handleEdit} disabled={!sede}>
            Editar
          </Button>
          <Button variant="danger" onClick={handleDelete} disabled={!sede}>
            Eliminar
          </Button>
          <Button variant="secondary" onClick={() => navigate(`/instituciones/${institutionId}/sedes`)}>
            Volver a las Sedes
          </Button>
        </div>
      </PageHeader>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Información General */}
        <Card>
          <CardHeader>
            <h3 className="text-lg font-semibold text-gray-900">Información General</h3>
          </CardHeader>
          <CardBody>
            <dl className="space-y-4">
              <div>
                <dt className="text-sm font-medium text-gray-500">ID de la Sede</dt>
                <dd className="mt-1 text-sm text-gray-900">{sede.id}</dd>
              </div>
              
              <div>
                <dt className="text-sm font-medium text-gray-500">Nombre</dt>
                <dd className="mt-1 text-sm text-gray-900">{sede.nombre}</dd>
              </div>

              <div>
                <dt className="text-sm font-medium text-gray-500">Dirección</dt>
                <dd className="mt-1 text-sm text-gray-900">{sede.direccion}</dd>
              </div>

              {sede.telefono && (
                <div>
                  <dt className="text-sm font-medium text-gray-500">Teléfono</dt>
                  <dd className="mt-1 text-sm text-gray-900">{sede.telefono}</dd>
                </div>
              )}
            </dl>
          </CardBody>
        </Card>

        {/* Información de la Institución */}
        <Card>
          <CardHeader>
            <h3 className="text-lg font-semibold text-gray-900">Institución</h3>
          </CardHeader>
          <CardBody>
            <dl className="space-y-4">
              <div>
                <dt className="text-sm font-medium text-gray-500">Institución</dt>
                <dd className="mt-1 text-sm text-gray-900">{sede.institucion.nombre}</dd>
              </div>

              <div>
                <dt className="text-sm font-medium text-gray-500">Siglas</dt>
                <dd className="mt-1">
                  <Badge variant="default">{sede.institucion.siglas}</Badge>
                </dd>
              </div>

              <div>
                <dt className="text-sm font-medium text-gray-500">ID de la Institución</dt>
                <dd className="mt-1 text-sm text-gray-900">{sede.institucion.id}</dd>
              </div>
            </dl>
          </CardBody>
        </Card>
      </div>

      {/* Información de Auditoría */}
      <Card>
        <CardHeader>
          <h3 className="text-lg font-semibold text-gray-900">Información de Auditoría</h3>
        </CardHeader>
        <CardBody>
          <dl className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <dt className="text-sm font-medium text-gray-500">Fecha de Creación</dt>
              <dd className="mt-1 text-sm text-gray-900">{formatDate(sede.created_at)}</dd>
            </div>
            
            <div>
              <dt className="text-sm font-medium text-gray-500">Última Actualización</dt>
              <dd className="mt-1 text-sm text-gray-900">{formatDate(sede.updated_at)}</dd>
            </div>
          </dl>
        </CardBody>
      </Card>
    </div>
  );
};

export default InstitutionSedeDetailPage; 