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

interface User {
  id: number;
  nombre: string;
  apellido: string;
  email: string;
  username: string;
  estado: string;
  institucion: {
    id: number;
    nombre: string;
  };
  roles: Array<{
    id: number;
    nombre: string;
  }>;
}

const UsersListPage = () => {
  const navigate = useNavigate();
  const { showSuccess, showError } = useAlertContext();
  const { dialogState, confirm, setLoading } = useConfirm();
  const [users, setUsers] = useState<User[]>([]);
  const [loading, setLoadingState] = useState(false);

  useEffect(() => {
    fetchUsers();
  }, []);

  const fetchUsers = async () => {
    setLoadingState(true);
    try {
      const response = await axiosClient.get('/users');
      console.log('Datos de usuarios recibidos:', response.data);
      console.log('Cantidad de usuarios en response.data:', response.data.length);
      console.log('Cantidad de usuarios en response.data.data:', response.data.data?.length);
      
      // Verificar la estructura de la respuesta
      if (response.data.data) {
        setUsers(response.data.data);
        console.log('Usuarios establecidos en el estado:', response.data.data.length);
      } else {
        setUsers(response.data);
        console.log('Usuarios establecidos en el estado (sin .data):', response.data.length);
      }
    } catch (err: any) {
      console.error('Error al cargar usuarios:', err);
      showError(err.response?.data?.message || 'Error al cargar los usuarios', 'Error');
    } finally {
      setLoadingState(false);
    }
  };

  const handleDelete = async (user: User) => {
    const confirmed = await confirm({
      title: 'Eliminar Usuario',
      message: `¿Está seguro de que desea eliminar al usuario ${user.nombre} ${user.apellido}? Esta acción no se puede deshacer.`,
      confirmText: 'Eliminar',
      cancelText: 'Cancelar',
      variant: 'danger'
    });

    if (confirmed) {
      setLoading(true);
      try {
        await axiosClient.delete(`/users/${user.id}`);
        showSuccess('Usuario eliminado exitosamente', 'Éxito');
        fetchUsers();
      } catch (err: any) {
        showError(err.response?.data?.message || 'Error al eliminar el usuario', 'Error');
      } finally {
        setLoading(false);
      }
    }
  };

  const handleBulkDelete = async (selectedUsers: User[]) => {
    const confirmed = await confirm({
      title: 'Eliminar Usuarios',
      message: `¿Está seguro de que desea eliminar ${selectedUsers.length} usuarios seleccionados? Esta acción no se puede deshacer.`,
      confirmText: 'Eliminar',
      cancelText: 'Cancelar',
      variant: 'danger'
    });

    if (confirmed) {
      setLoading(true);
      try {
        await Promise.all(selectedUsers.map(user => 
          axiosClient.delete(`/users/${user.id}`)
        ));
        showSuccess(`${selectedUsers.length} usuarios eliminados exitosamente`, 'Éxito');
        fetchUsers();
      } catch (err: any) {
        showError('Error al eliminar algunos usuarios', 'Error');
      } finally {
        setLoading(false);
      }
    }
  };

  const columns: Column<User>[] = [
    {
      key: 'usuario',
      header: 'Usuario',
      accessor: (user) => (
        <div className="flex items-center">
          <div className="flex-shrink-0 h-10 w-10">
            <div className="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center">
              <span className="text-sm font-medium text-primary-700">
                {user.nombre.charAt(0)}{user.apellido.charAt(0)}
              </span>
            </div>
          </div>
          <div className="ml-4">
            <div className="text-sm font-medium text-gray-900">
              {user.nombre} {user.apellido}
            </div>
            <div className="text-sm text-gray-500">
              @{user.username}
            </div>
          </div>
        </div>
      ),
      sortable: true,
    },
    {
      key: 'email',
      header: 'Email',
      accessor: (user) => (
        <span className="text-sm text-gray-500">{user.email}</span>
      ),
      sortable: true,
    },
    {
      key: 'institucion',
      header: 'Institución',
      accessor: (user) => (
        <span className="text-sm text-gray-500">{user.institucion?.nombre}</span>
      ),
      sortable: true,
    },
    {
      key: 'roles',
      header: 'Roles',
      accessor: (user) => (
        <div className="flex flex-wrap gap-1">
          {user.roles?.map((role) => (
            <Badge key={role.id} variant="primary" size="sm">
              {role.nombre}
            </Badge>
          ))}
        </div>
      ),
      sortable: false,
    },
    {
      key: 'estado',
      header: 'Estado',
      accessor: (user) => (
        <Badge
          variant={user.estado === 'activo' ? 'success' : 'error'}
          size="sm"
        >
          {user.estado}
        </Badge>
      ),
      sortable: true,
      align: 'center',
    },
  ];

  const actions: ActionButton<User>[] = [
    {
      label: 'Ver',
      variant: 'ghost',
      size: 'sm',
      onClick: (user) => navigate(`/usuarios/${user.id}`),
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
      onClick: (user) => navigate(`/usuarios/${user.id}/editar`),
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

  const bulkActions: ActionButton<User[]>[] = [
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

  // Log para debuggear
  console.log('Render - Cantidad de usuarios en el estado:', users.length);

  return (
    <div className="space-y-6">
      <PageHeader
        title="Usuarios"
        description="Gestione los usuarios del sistema y sus permisos."
      >
        <Link to="/usuarios/crear">
          <Button>
            <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Crear Usuario
          </Button>
        </Link>
      </PageHeader>

      <DataTable
        data={users}
        columns={columns}
        actions={actions}
        loading={loading}
        searchable={true}
        searchKeys={['nombre', 'apellido', 'email', 'username', 'institucion.nombre']}
        searchPlaceholder="Buscar usuarios..."
        sortable={true}
        pagination={true}
        selectable={true}
        bulkActions={bulkActions}
        emptyMessage="No hay usuarios registrados"
        emptyIcon={
          <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
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

export default UsersListPage; 