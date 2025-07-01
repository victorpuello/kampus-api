import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Link } from 'react-router-dom';
import axiosClient from '../api/axiosClient';
import { useAlertContext } from '../contexts/AlertContext';
import { useConfirm } from '../hooks/useConfirm';
import { 
  DataTable, 
  Button, 
  Badge, 
  PageHeader,
  ConfirmDialog,
  type Column, 
  type ActionButton 
} from '../components/ui';

interface Guardian {
  id: number;
  user_id: number;
  nombre: string;
  telefono: string;
  email: string;
}

const GuardiansListPage = () => {
  const navigate = useNavigate();
  const { showSuccess, showError } = useAlertContext();
  const { dialogState, confirm, setLoading } = useConfirm();
  const [guardians, setGuardians] = useState<Guardian[]>([]);
  const [loading, setLoadingState] = useState(false);

  useEffect(() => {
    fetchGuardians();
  }, []);

  const fetchGuardians = async () => {
    setLoadingState(true);
    try {
      const response = await axiosClient.get('/acudientes');
      console.log('Datos de acudientes recibidos:', response.data);
      setGuardians(response.data.data);
    } catch (err: any) {
      console.error('Error al cargar acudientes:', err);
      showError(err.response?.data?.message || 'Error al cargar los acudientes', 'Error');
    } finally {
      setLoadingState(false);
    }
  };

  const handleDelete = async (guardian: Guardian) => {
    const confirmed = await confirm({
      title: 'Eliminar Acudiente',
      message: `¿Está seguro de que desea eliminar al acudiente ${guardian.nombre}? Esta acción no se puede deshacer.`,
      confirmText: 'Eliminar',
      cancelText: 'Cancelar',
      variant: 'danger'
    });

    if (confirmed) {
      setLoading(true);
      try {
        await axiosClient.delete(`/acudientes/${guardian.id}`);
        showSuccess('Acudiente eliminado exitosamente', 'Éxito');
        fetchGuardians();
      } catch (err: any) {
        showError(err.response?.data?.message || 'Error al eliminar el acudiente', 'Error');
      } finally {
        setLoading(false);
      }
    }
  };

  const handleBulkDelete = async (selectedGuardians: Guardian[]) => {
    const confirmed = await confirm({
      title: 'Eliminar Acudientes',
      message: `¿Está seguro de que desea eliminar ${selectedGuardians.length} acudientes seleccionados? Esta acción no se puede deshacer.`,
      confirmText: 'Eliminar',
      cancelText: 'Cancelar',
      variant: 'danger'
    });

    if (confirmed) {
      setLoading(true);
      try {
        await Promise.all(selectedGuardians.map(guardian => 
          axiosClient.delete(`/acudientes/${guardian.id}`)
        ));
        showSuccess(`${selectedGuardians.length} acudientes eliminados exitosamente`, 'Éxito');
        fetchGuardians();
      } catch (err: any) {
        showError('Error al eliminar algunos acudientes', 'Error');
      } finally {
        setLoading(false);
      }
    }
  };

  const columns: Column<Guardian>[] = [
    {
      key: 'nombre',
      header: 'Nombre',
      accessor: (guardian) => (
        <div>
          <div className="font-medium text-gray-900">
            {guardian.nombre}
          </div>
        </div>
      ),
      sortable: true,
    },
    {
      key: 'contacto',
      header: 'Contacto',
      accessor: (guardian) => (
        <div>
          <div className="text-sm text-gray-900">{guardian.email}</div>
          <div className="text-sm text-gray-500">{guardian.telefono}</div>
        </div>
      ),
      sortable: true,
    },
  ];

  const actions: ActionButton<Guardian>[] = [
    {
      label: 'Ver',
      variant: 'ghost',
      size: 'sm',
      onClick: (guardian) => navigate(`/acudientes/${guardian.id}`),
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
      onClick: (guardian) => navigate(`/acudientes/${guardian.id}/editar`),
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

  const bulkActions: ActionButton<Guardian[]>[] = [
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
      <PageHeader
        title="Acudientes"
        description="Gestione los acudientes registrados en el sistema."
      >
        <Link to="/acudientes/crear">
          <Button>
            <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Crear Acudiente
          </Button>
        </Link>
      </PageHeader>

      <DataTable
        data={guardians}
        columns={columns}
        actions={actions}
        loading={loading}
        searchable={true}
        searchKeys={['nombre', 'email', 'telefono']}
        searchPlaceholder="Buscar acudientes..."
        sortable={true}
        pagination={true}
        selectable={true}
        bulkActions={bulkActions}
        emptyMessage="No hay acudientes registrados"
        emptyIcon={
          <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
        }
      />

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

export default GuardiansListPage; 