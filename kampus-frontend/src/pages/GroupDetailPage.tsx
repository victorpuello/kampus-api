import { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import axiosClient from '../api/axiosClient';
import { Button } from '../components/ui/Button';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
import { Badge } from '../components/ui/Badge';
import { useAlertContext } from '../contexts/AlertContext';
import { useConfirm } from '../hooks/useConfirm';
import ConfirmDialog from '../components/ui/ConfirmDialog';
import { MatriculaEstudianteModal } from '../components/grupos/MatriculaEstudianteModal';

interface Grupo {
  id: number;
  nombre: string;
  descripcion?: string;
  grado_id: number;
  grado?: {
    id: number;
    nombre: string;
    nivel?: string;
  };
  capacidad_maxima?: number;
  estado: string;
  estudiantes?: Array<{
    id: number;
    nombre?: string;
    apellido?: string;
    email?: string;
    estado: string;
    user?: {
      nombre: string;
      apellido: string;
      email?: string;
    };
  }>;
}

const GroupDetailPage = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const { showSuccess, showError } = useAlertContext();
  const { dialogState, confirm, setLoading: setConfirmLoading } = useConfirm();
  const [grupo, setGrupo] = useState<Grupo | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [showMatriculaModal, setShowMatriculaModal] = useState(false);

    const fetchGrupo = async () => {
    try {
      const response = await axiosClient.get(`/grupos/${id}`);
      console.log('Datos del grupo recibidos:', response.data);
      setGrupo(response.data.data || response.data);
      setError(null);
    } catch (err: any) {
      console.error('Error al cargar grupo:', err);
      setError(err.response?.data?.message || 'Error al cargar el grupo');
    } finally {
      setLoading(false);
    }
  };

  const handleEstudianteMatriculado = () => {
    fetchGrupo(); // Recargar datos del grupo
  };

  useEffect(() => {
    fetchGrupo();
  }, [id]);

  const handleDelete = async () => {
    const grupoName = grupo?.nombre || 'este grupo';
    const confirmed = await confirm({
      title: 'Eliminar Grupo',
      message: `¿Estás seguro de que deseas eliminar el grupo "${grupoName}"? Esta acción no se puede deshacer.`,
      confirmText: 'Eliminar',
      cancelText: 'Cancelar',
      variant: 'danger'
    });

    if (!confirmed) return;

    try {
      setConfirmLoading(true);
      await axiosClient.delete(`/grupos/${id}`);
      showSuccess('Grupo eliminado exitosamente', 'Éxito');
      navigate('/grupos');
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || 'Error al eliminar el grupo';
      showError(errorMessage, 'Error');
      setError(errorMessage);
    } finally {
      setConfirmLoading(false);
    }
  };

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
          <div className="ml-3">
            <h3 className="text-sm font-medium text-red-800">{error}</h3>
          </div>
        </div>
      </div>
    );
  }

  if (!grupo) {
    return (
      <div className="text-center">
        <h3 className="text-lg font-medium text-gray-900">Grupo no encontrado</h3>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div className="sm:flex sm:items-center sm:justify-between flex-wrap">
        <div>
          <h1 className="text-2xl font-semibold text-gray-900">
            {grupo.nombre}
          </h1>
          <p className="mt-2 text-sm text-gray-700">
            {grupo.grado?.nombre} {grupo.grado?.nivel && `(${grupo.grado.nivel})`} • Grupo Académico
          </p>
        </div>
        <div className="mt-4 sm:mt-0 flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
          <Button
            variant="primary"
            onClick={() => navigate(`/grupos/${id}/editar`)}
            leftIcon={
              <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
            }
          >
            Editar
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
            Eliminar
          </Button>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Información del Grupo */}
        <Card>
          <CardHeader>
            <h3 className="text-lg font-semibold text-gray-900">Información del Grupo</h3>
          </CardHeader>
          <CardBody>
            <div className="space-y-4">
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Nombre:</span>
                <span className="text-sm text-gray-900">{grupo.nombre}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Grado:</span>
                <span className="text-sm text-gray-900">
                  {grupo.grado?.nombre} {grupo.grado?.nivel && `(${grupo.grado.nivel})`}
                </span>
              </div>
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Descripción:</span>
                <span className="text-sm text-gray-900">
                  {grupo.descripcion || 'Sin descripción'}
                </span>
              </div>
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Estado:</span>
                <Badge
                  variant={grupo.estado === 'activo' ? 'success' : 'error'}
                >
                  {grupo.estado}
                </Badge>
              </div>
            </div>
          </CardBody>
        </Card>

        {/* Estadísticas */}
        <Card>
          <CardHeader>
            <h3 className="text-lg font-semibold text-gray-900">Estadísticas</h3>
          </CardHeader>
          <CardBody>
            <div className="space-y-4">
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Total de Estudiantes:</span>
                <span className="text-sm text-gray-900">{grupo.estudiantes?.length || 0}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Capacidad Máxima:</span>
                <span className="text-sm text-gray-900">
                  {grupo.capacidad_maxima || 'Sin límite'}
                </span>
              </div>
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Estudiantes Activos:</span>
                <span className="text-sm text-gray-900">
                  {grupo.estudiantes?.filter(e => e.estado === 'activo').length || 0}
                </span>
              </div>
              {grupo.capacidad_maxima && (
                <div className="flex justify-between">
                  <span className="text-sm font-medium text-gray-500">Ocupación:</span>
                  <span className="text-sm text-gray-900">
                    {Math.round(((grupo.estudiantes?.length || 0) / grupo.capacidad_maxima) * 100)}%
                  </span>
                </div>
              )}
            </div>
          </CardBody>
        </Card>
      </div>

      {/* Estudiantes Matriculados */}
      <Card>
        <CardHeader>
          <div className="flex items-center justify-between">
            <h3 className="text-lg font-semibold text-gray-900">Estudiantes Matriculados</h3>
            <Button
              variant="secondary"
              onClick={() => setShowMatriculaModal(true)}
              leftIcon={
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
              }
            >
              Matricular Estudiante
            </Button>
          </div>
        </CardHeader>
        <CardBody>
          {grupo.estudiantes && grupo.estudiantes.length > 0 ? (
            <div className="overflow-x-auto">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Estudiante
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Email
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Estado
                    </th>
                    <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Acciones
                    </th>
                  </tr>
                </thead>
                <tbody className="bg-white divide-y divide-gray-200">
                  {grupo.estudiantes.map((estudiante) => (
                    <tr key={estudiante.id} className="hover:bg-gray-50">
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div className="flex items-center">
                          <div className="flex-shrink-0 h-10 w-10">
                            <div className="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                              <span className="text-sm font-medium text-gray-700">
                                {(estudiante.user?.nombre || estudiante.nombre || '').charAt(0)}
                                {(estudiante.user?.apellido || estudiante.apellido || '').charAt(0)}
                              </span>
                            </div>
                          </div>
                          <div className="ml-4">
                            <div className="text-sm font-medium text-gray-900">
                              {estudiante.user?.nombre || estudiante.nombre || 'Sin nombre'} {estudiante.user?.apellido || estudiante.apellido || 'Sin apellido'}
                            </div>
                          </div>
                        </div>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div className="text-sm text-gray-500">
                          {estudiante.user?.email || estudiante.email || 'Sin email'}
                        </div>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <Badge
                          variant={estudiante.estado === 'activo' ? 'success' : 'error'}
                        >
                          {estudiante.estado}
                        </Badge>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => navigate(`/estudiantes/${estudiante.id}`)}
                        >
                          Ver
                        </Button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          ) : (
            <div className="text-center py-8">
              <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
              </svg>
              <h3 className="mt-2 text-sm font-medium text-gray-900">No hay estudiantes matriculados</h3>
              <p className="mt-1 text-sm text-gray-500">
                Este grupo no tiene estudiantes matriculados.
              </p>
              <div className="mt-6">
                <Button
                  variant="primary"
                  onClick={() => setShowMatriculaModal(true)}
                  leftIcon={
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                  }
                >
                  Matricular Estudiante
                </Button>
              </div>
            </div>
          )}
        </CardBody>
      </Card>

      {/* Acciones adicionales */}
      <Card>
        <CardHeader>
          <h3 className="text-lg font-semibold text-gray-900">Acciones</h3>
        </CardHeader>
        <CardBody>
          <div className="flex flex-wrap gap-3">
            <Button
              variant="secondary"
              onClick={() => navigate('/grupos')}
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
              onClick={() => navigate(`/grupos/${id}/editar`)}
              leftIcon={
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              }
            >
              Editar Grupo
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
              Eliminar Grupo
            </Button>
          </div>
        </CardBody>
      </Card>

      {/* Modal de Matriculación */}
      <MatriculaEstudianteModal
        isOpen={showMatriculaModal}
        onClose={() => setShowMatriculaModal(false)}
        grupoId={Number(id)}
        onEstudianteMatriculado={handleEstudianteMatriculado}
      />

      {/* ConfirmDialog */}
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

export default GroupDetailPage; 