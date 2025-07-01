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

interface User {
  id: number;
  nombre: string;
  apellido: string;
  email: string;
  username: string;
  tipo_documento: string;
  numero_documento: string;
  estado: string;
  institucion: {
    id: number;
    nombre: string;
  };
  roles: Array<{
    id: number;
    nombre: string;
    permissions: Array<{
      id: number;
      nombre: string;
    }>;
  }>;
}

const UserDetailPage = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const { showSuccess, showError } = useAlertContext();
  const { dialogState, confirm, setLoading: setConfirmLoading } = useConfirm();
  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoadingState] = useState(false);

  useEffect(() => {
    if (id) {
      fetchUser();
    }
  }, [id]);

  const fetchUser = async () => {
    setLoadingState(true);
    try {
      const response = await axiosClient.get(`/users/${id}`);
      console.log('Datos del usuario recibidos:', response.data);
      setUser(response.data.data);
    } catch (err: any) {
      console.error('Error al cargar usuario:', err);
      showError(err.response?.data?.message || 'Error al cargar el usuario', 'Error');
    } finally {
      setLoadingState(false);
    }
  };

  const handleDelete = async () => {
    if (!user) return;

    const confirmed = await confirm({
      title: 'Eliminar Usuario',
      message: `¿Está seguro de que desea eliminar al usuario ${user.nombre} ${user.apellido}? Esta acción no se puede deshacer.`,
      confirmText: 'Eliminar',
      cancelText: 'Cancelar',
      variant: 'danger'
    });

    if (confirmed) {
      setConfirmLoading(true);
      try {
        await axiosClient.delete(`/users/${user.id}`);
        showSuccess('Usuario eliminado exitosamente', 'Éxito');
        navigate('/usuarios');
      } catch (err: any) {
        showError(err.response?.data?.message || 'Error al eliminar el usuario', 'Error');
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
          <p className="mt-4 text-gray-600">Cargando usuario...</p>
        </div>
      </div>
    );
  }

  if (!user) {
    return (
      <div className="text-center py-12">
        <h3 className="text-lg font-medium text-gray-900">Usuario no encontrado</h3>
        <p className="mt-2 text-gray-600">El usuario que busca no existe o ha sido eliminado.</p>
        <Button 
          onClick={() => navigate('/usuarios')}
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
        title={`${user.nombre} ${user.apellido}`}
        description="Información detallada del usuario del sistema"
      >
        <div className="flex space-x-3">
          <Button
            variant="secondary"
            onClick={() => navigate(`/usuarios/${user.id}/editar`)}
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

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Información Personal */}
        <Card>
          <CardHeader>
            <h3 className="text-lg font-semibold text-gray-900">Información Personal</h3>
          </CardHeader>
          <CardBody>
            <dl className="space-y-4">
              <div>
                <dt className="text-sm font-medium text-gray-500">Nombre Completo</dt>
                <dd className="mt-1 text-sm text-gray-900">
                  {user.nombre} {user.apellido}
                </dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-gray-500">Email</dt>
                <dd className="mt-1 text-sm text-gray-900">{user.email}</dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-gray-500">Nombre de Usuario</dt>
                <dd className="mt-1 text-sm text-gray-900">@{user.username}</dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-gray-500">Documento</dt>
                <dd className="mt-1 text-sm text-gray-900">
                  {user.tipo_documento} {user.numero_documento}
                </dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-gray-500">Estado</dt>
                <dd className="mt-1">
                  <Badge
                    variant={user.estado === 'activo' ? 'success' : 'error'}
                  >
                    {user.estado}
                  </Badge>
                </dd>
              </div>
            </dl>
          </CardBody>
        </Card>

        {/* Información Institucional */}
        <Card>
          <CardHeader>
            <h3 className="text-lg font-semibold text-gray-900">Información Institucional</h3>
          </CardHeader>
          <CardBody>
            <dl className="space-y-4">
              <div>
                <dt className="text-sm font-medium text-gray-500">Institución</dt>
                <dd className="mt-1 text-sm text-gray-900">{user.institucion?.nombre}</dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-gray-500">Roles Asignados</dt>
                <dd className="mt-1">
                  <div className="flex flex-wrap gap-2">
                    {user.roles?.map((role) => (
                      <Badge key={role.id} variant="primary">
                        {role.nombre}
                      </Badge>
                    ))}
                  </div>
                </dd>
              </div>
            </dl>
          </CardBody>
        </Card>
      </div>

      {/* Permisos Detallados */}
      {user.roles && user.roles.length > 0 && (
        <Card>
          <CardHeader>
            <h3 className="text-lg font-semibold text-gray-900">Permisos por Rol</h3>
          </CardHeader>
          <CardBody>
            <div className="space-y-6">
              {user.roles.map((role) => (
                <div key={role.id} className="border-t pt-4 first:border-t-0 first:pt-0">
                  <h4 className="text-sm font-medium text-gray-700 mb-3">
                    <Badge variant="primary" className="mr-2">{role.nombre}</Badge>
                    Permisos:
                  </h4>
                  <div className="flex flex-wrap gap-2">
                    {role.permissions && role.permissions.length > 0 ? (
                      role.permissions.map((permission) => (
                                                 <Badge key={permission.id} variant="default" size="sm">
                          {permission.nombre}
                        </Badge>
                      ))
                    ) : (
                      <p className="text-sm text-gray-500">Sin permisos específicos</p>
                    )}
                  </div>
                </div>
              ))}
            </div>
          </CardBody>
        </Card>
      )}

      {/* Acciones */}
      <Card>
        <CardHeader>
          <h3 className="text-lg font-semibold text-gray-900">Acciones</h3>
        </CardHeader>
        <CardBody>
          <div className="flex flex-wrap gap-3">
            <Button
              variant="secondary"
              onClick={() => navigate('/usuarios')}
              leftIcon={
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
              }
            >
              Volver a la Lista
            </Button>
            <Button
              variant="primary"
              onClick={() => navigate(`/usuarios/${user.id}/editar`)}
              leftIcon={
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              }
            >
              Editar Usuario
            </Button>
            <Button
              variant="danger"
              onClick={handleDelete}
              leftIcon={
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              }
            >
              Eliminar Usuario
            </Button>
          </div>
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

export default UserDetailPage; 