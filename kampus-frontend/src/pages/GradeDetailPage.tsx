import { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import axiosClient from '../api/axiosClient';
import { Button } from '../components/ui/Button';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
import { Badge } from '../components/ui/Badge';
import { useAlertContext } from '../contexts/AlertContext';
import { useConfirm } from '../hooks/useConfirm';
import ConfirmDialog from '../components/ui/ConfirmDialog';

interface Grado {
  id: number;
  nombre: string;
  descripcion?: string;
  nivel?: string;
  estado: string;
  grupos?: Array<{
    id: number;
    nombre: string;
    descripcion?: string;
    estado: string;
    estudiantes_count?: number;
  }>;
}

const GradeDetailPage = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const { showSuccess, showError } = useAlertContext();
  const { dialogState, confirm, setLoading: setConfirmLoading } = useConfirm();
  const [grado, setGrado] = useState<Grado | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchGrado = async () => {
      try {
        const response = await axiosClient.get(`/grados/${id}`);
        console.log('Datos del grado recibidos:', response.data);
        setGrado(response.data.data || response.data);
        setError(null);
      } catch (err: any) {
        console.error('Error al cargar grado:', err);
        setError(err.response?.data?.message || 'Error al cargar el grado');
      } finally {
        setLoading(false);
      }
    };

    fetchGrado();
  }, [id]);

  const handleDelete = async () => {
    const gradoName = grado?.nombre || 'este grado';
    const confirmed = await confirm({
      title: 'Eliminar Grado',
      message: `¿Estás seguro de que deseas eliminar el grado "${gradoName}"? Esta acción no se puede deshacer.`,
      confirmText: 'Eliminar',
      cancelText: 'Cancelar',
      variant: 'danger'
    });

    if (!confirmed) return;

    try {
      setConfirmLoading(true);
      await axiosClient.delete(`/grados/${id}`);
      showSuccess('Grado eliminado exitosamente', 'Éxito');
      navigate('/grados');
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || 'Error al eliminar el grado';
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

  if (!grado) {
    return (
      <div className="text-center">
        <h3 className="text-lg font-medium text-gray-900">Grado no encontrado</h3>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div className="sm:flex sm:items-center sm:justify-between flex-wrap">
        <div>
          <h1 className="text-2xl font-semibold text-gray-900">
            {grado.nombre}
          </h1>
          <p className="mt-2 text-sm text-gray-700">
            {grado.nivel && `${grado.nivel} • `}Grado Académico
          </p>
        </div>
        <div className="mt-4 sm:mt-0 flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
          <Button
            variant="primary"
            onClick={() => navigate(`/grados/${id}/editar`)}
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
        {/* Información del Grado */}
        <Card>
          <CardHeader>
            <h3 className="text-lg font-semibold text-gray-900">Información del Grado</h3>
          </CardHeader>
          <CardBody>
            <div className="space-y-4">
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Nombre:</span>
                <span className="text-sm text-gray-900">{grado.nombre}</span>
              </div>
              {grado.nivel && (
                <div className="flex justify-between">
                  <span className="text-sm font-medium text-gray-500">Nivel:</span>
                  <span className="text-sm text-gray-900">{grado.nivel}</span>
                </div>
              )}
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Descripción:</span>
                <span className="text-sm text-gray-900">
                  {grado.descripcion || 'Sin descripción'}
                </span>
              </div>
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Estado:</span>
                <Badge
                  variant={grado.estado === 'activo' ? 'success' : 'error'}
                >
                  {grado.estado}
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
                <span className="text-sm font-medium text-gray-500">Total de Grupos:</span>
                <span className="text-sm text-gray-900">{grado.grupos?.length || 0}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Grupos Activos:</span>
                <span className="text-sm text-gray-900">
                  {grado.grupos?.filter(g => g.estado === 'activo').length || 0}
                </span>
              </div>
            </div>
          </CardBody>
        </Card>
      </div>

      {/* Grupos Asociados */}
      <Card>
        <CardHeader>
          <div className="flex items-center justify-between">
            <h3 className="text-lg font-semibold text-gray-900">Grupos Asociados</h3>
            <Button
              variant="secondary"
              onClick={() => navigate('/grupos/crear')}
              leftIcon={
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
              }
            >
              Crear Grupo
            </Button>
          </div>
        </CardHeader>
        <CardBody>
          {grado.grupos && grado.grupos.length > 0 ? (
            <div className="overflow-x-auto">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Grupo
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Descripción
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Estudiantes
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
                  {grado.grupos.map((grupo) => (
                    <tr key={grupo.id} className="hover:bg-gray-50">
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div className="text-sm font-medium text-gray-900">{grupo.nombre}</div>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div className="text-sm text-gray-500">
                          {grupo.descripcion || 'Sin descripción'}
                        </div>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div className="text-sm text-gray-500">
                          {grupo.estudiantes_count || 0} estudiantes
                        </div>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <Badge
                          variant={grupo.estado === 'activo' ? 'success' : 'error'}
                        >
                          {grupo.estado}
                        </Badge>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => navigate(`/grupos/${grupo.id}`)}
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
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              <h3 className="mt-2 text-sm font-medium text-gray-900">No hay grupos</h3>
              <p className="mt-1 text-sm text-gray-500">
                Este grado no tiene grupos asociados.
              </p>
              <div className="mt-6">
                <Button
                  variant="primary"
                  onClick={() => navigate('/grupos/crear')}
                  leftIcon={
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                  }
                >
                  Crear Grupo
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
              onClick={() => navigate('/grados')}
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
              onClick={() => navigate(`/grados/${id}/editar`)}
              leftIcon={
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              }
            >
              Editar Grado
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
              Eliminar Grado
            </Button>
          </div>
        </CardBody>
      </Card>

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

export default GradeDetailPage; 