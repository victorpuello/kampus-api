import { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import axiosClient from '../api/axiosClient';
import { useAlertContext } from '../contexts/AlertContext';
import { useConfirm } from '../hooks/useConfirm';
import { 
  Card, 
  CardHeader, 
  CardBody, 
  Button, 
  Badge, 
  PageHeader,
  ConfirmDialog 
} from '../components/ui';

interface Guardian {
  id: number;
  user_id: number;
  nombre: string;
  telefono: string;
  email: string;
}

const GuardianDetailPage = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const { showSuccess, showError } = useAlertContext();
  const { dialogState, confirm, setLoading: setConfirmLoading } = useConfirm();
  const [guardian, setGuardian] = useState<Guardian | null>(null);
  const [loading, setLoadingState] = useState(false);

  useEffect(() => {
    if (id) {
      fetchGuardian();
    }
  }, [id]);

  const fetchGuardian = async () => {
    setLoadingState(true);
    try {
      const response = await axiosClient.get(`/acudientes/${id}`);
      console.log('Datos del acudiente recibidos:', response.data);
      setGuardian(response.data.data);
    } catch (err: any) {
      console.error('Error al cargar acudiente:', err);
      showError(err.response?.data?.message || 'Error al cargar el acudiente', 'Error');
    } finally {
      setLoadingState(false);
    }
  };

  const handleDelete = async () => {
    if (!guardian) return;

    const confirmed = await confirm({
      title: 'Eliminar Acudiente',
      message: `¿Está seguro de que desea eliminar al acudiente ${guardian.nombre}? Esta acción no se puede deshacer.`,
      confirmText: 'Eliminar',
      cancelText: 'Cancelar',
      variant: 'danger'
    });

    if (confirmed) {
      setConfirmLoading(true);
      try {
        await axiosClient.delete(`/acudientes/${guardian.id}`);
        showSuccess('Acudiente eliminado exitosamente', 'Éxito');
        navigate('/acudientes');
      } catch (err: any) {
        showError(err.response?.data?.message || 'Error al eliminar el acudiente', 'Error');
      } finally {
        setConfirmLoading(false);
      }
    }
  };

  if (loading) {
    return (
      <div className="flex justify-center items-center h-64">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mx-auto"></div>
          <p className="mt-4 text-gray-600">Cargando acudiente...</p>
        </div>
      </div>
    );
  }

  if (!guardian) {
    return (
      <div className="text-center py-12">
        <h3 className="text-lg font-medium text-gray-900">Acudiente no encontrado</h3>
        <p className="mt-2 text-gray-600">El acudiente que busca no existe o ha sido eliminado.</p>
        <Button 
          onClick={() => navigate('/acudientes')}
          className="mt-4"
        >
          Volver a la lista
        </Button>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <PageHeader
        title={guardian.nombre}
        description="Información detallada del acudiente"
      >
        <div className="flex space-x-3">
          <Button
            variant="secondary"
            onClick={() => navigate(`/acudientes/${guardian.id}/editar`)}
          >
            <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Editar
          </Button>
          <Button
            variant="danger"
            onClick={handleDelete}
          >
            <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Eliminar
          </Button>
        </div>
      </PageHeader>

      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold text-gray-900">Información del Acudiente</h2>
        </CardHeader>
        <CardBody>
          <dl className="space-y-4">
            <div>
              <dt className="text-sm font-medium text-gray-500">Nombre</dt>
              <dd className="mt-1 text-sm text-gray-900">
                {guardian.nombre}
              </dd>
            </div>
            <div>
              <dt className="text-sm font-medium text-gray-500">Email</dt>
              <dd className="mt-1 text-sm text-gray-900">{guardian.email}</dd>
            </div>
            <div>
              <dt className="text-sm font-medium text-gray-500">Teléfono</dt>
              <dd className="mt-1 text-sm text-gray-900">{guardian.telefono}</dd>
            </div>
            <div>
              <dt className="text-sm font-medium text-gray-500">ID de Usuario</dt>
              <dd className="mt-1 text-sm text-gray-900">{guardian.user_id}</dd>
            </div>
          </dl>
        </CardBody>
      </Card>

      <ConfirmDialog 
        isOpen={dialogState.isOpen}
        title={dialogState.title || 'Confirmar Acción'}
        message={dialogState.message}
        confirmText={dialogState.confirmText}
        cancelText={dialogState.cancelText}
        variant={dialogState.variant}
        onConfirm={dialogState.onConfirm}
        onCancel={dialogState.onCancel}
        loading={dialogState.loading}
      />
    </div>
  );
};

export default GuardianDetailPage; 