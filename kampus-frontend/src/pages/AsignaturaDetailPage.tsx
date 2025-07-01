import { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import axiosClient from '../api/axiosClient';
import { Button } from '../components/ui/Button';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
import { Badge } from '../components/ui/Badge';
import { useAlertContext } from '../contexts/AlertContext';
import { useConfirm } from '../hooks/useConfirm';
import ConfirmDialog from '../components/ui/ConfirmDialog';

interface Asignatura {
  id: number;
  nombre: string;
  codigo?: string;
  descripcion?: string;
  creditos?: number;
  estado: string;
  area?: {
    id: number;
    nombre: string;
    codigo?: string;
    color?: string;
  };
  grados?: Array<{
    id: number;
    nombre: string;
    nivel?: string;
  }>;
}

const AsignaturaDetailPage = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const { showSuccess, showError } = useAlertContext();
  const { dialogState, confirm, setLoading: setConfirmLoading } = useConfirm();
  const [asignatura, setAsignatura] = useState<Asignatura | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchAsignatura = async () => {
      try {
        const response = await axiosClient.get(`/asignaturas/${id}`);
        console.log('Datos de la asignatura recibidos:', response.data);
        setAsignatura(response.data.data || response.data);
        setError(null);
      } catch (err: any) {
        console.error('Error al cargar asignatura:', err);
        setError(err.response?.data?.message || 'Error al cargar la asignatura');
      } finally {
        setLoading(false);
      }
    };

    fetchAsignatura();
  }, [id]);

  const handleDelete = async () => {
    const asignaturaName = asignatura?.nombre || 'esta asignatura';
    const confirmed = await confirm({
      title: 'Eliminar Asignatura',
      message: `¿Estás seguro de que deseas eliminar la asignatura "${asignaturaName}"? Esta acción no se puede deshacer.`,
      confirmText: 'Eliminar',
      cancelText: 'Cancelar',
      variant: 'danger'
    });

    if (!confirmed) return;

    try {
      setConfirmLoading(true);
      await axiosClient.delete(`/asignaturas/${id}`);
      showSuccess('Asignatura eliminada exitosamente', 'Éxito');
      navigate('/asignaturas');
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || 'Error al eliminar la asignatura';
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

  if (!asignatura) {
    return (
      <div className="text-center">
        <h3 className="text-lg font-medium text-gray-900">Asignatura no encontrada</h3>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div className="sm:flex sm:items-center sm:justify-between flex-wrap">
        <div>
          <div className="flex items-center">
            <div 
              className="h-12 w-12 rounded-full flex items-center justify-center mr-4"
              style={{ backgroundColor: asignatura.area?.color || '#3B82F6' }}
            >
              <span className="text-lg font-medium text-white">
                {asignatura.nombre.charAt(0)}
              </span>
            </div>
            <div>
              <h1 className="text-2xl font-semibold text-gray-900">
                {asignatura.nombre}
              </h1>
              <p className="mt-1 text-sm text-gray-700">
                {asignatura.codigo && `${asignatura.codigo} • `}Asignatura
              </p>
            </div>
          </div>
        </div>
        <div className="mt-4 sm:mt-0 flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
          <Button
            variant="primary"
            onClick={() => navigate(`/asignaturas/${id}/editar`)}
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
        {/* Información de la Asignatura */}
        <Card>
          <CardHeader>
            <h3 className="text-lg font-semibold text-gray-900">Información de la Asignatura</h3>
          </CardHeader>
          <CardBody>
            <div className="space-y-4">
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Nombre:</span>
                <span className="text-sm text-gray-900">{asignatura.nombre}</span>
              </div>
              {asignatura.codigo && (
                <div className="flex justify-between">
                  <span className="text-sm font-medium text-gray-500">Código:</span>
                  <span className="text-sm text-gray-900">{asignatura.codigo}</span>
                </div>
              )}
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Descripción:</span>
                <span className="text-sm text-gray-900">
                  {asignatura.descripcion || 'Sin descripción'}
                </span>
              </div>
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Créditos:</span>
                <span className="text-sm text-gray-900">
                  {asignatura.creditos || 0} créditos
                </span>
              </div>
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Estado:</span>
                <Badge
                  variant={asignatura.estado === 'activo' ? 'success' : 'error'}
                >
                  {asignatura.estado}
                </Badge>
              </div>
            </div>
          </CardBody>
        </Card>

        {/* Área Asociada */}
        <Card>
          <CardHeader>
            <h3 className="text-lg font-semibold text-gray-900">Área Asociada</h3>
          </CardHeader>
          <CardBody>
            {asignatura.area ? (
              <div className="space-y-4">
                <div className="flex items-center">
                  <div 
                    className="w-8 h-8 rounded-full flex items-center justify-center mr-3"
                    style={{ backgroundColor: asignatura.area.color || '#3B82F6' }}
                  >
                    <span className="text-sm font-medium text-white">
                      {asignatura.area.nombre.charAt(0)}
                    </span>
                  </div>
                  <div>
                    <div className="text-sm font-medium text-gray-900">
                      {asignatura.area.nombre}
                    </div>
                    {asignatura.area.codigo && (
                      <div className="text-sm text-gray-500">
                        {asignatura.area.codigo}
                      </div>
                    )}
                  </div>
                </div>
                <Button
                  variant="secondary"
                  size="sm"
                  onClick={() => navigate(`/areas/${asignatura.area.id}`)}
                  leftIcon={
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  }
                >
                  Ver Área
                </Button>
              </div>
            ) : (
              <div className="text-center py-4">
                <p className="text-sm text-gray-500">Sin área asociada</p>
              </div>
            )}
          </CardBody>
        </Card>
      </div>

      {/* Grados donde se imparte */}
      <Card>
        <CardHeader>
          <div className="flex items-center justify-between">
            <h3 className="text-lg font-semibold text-gray-900">Grados donde se imparte</h3>
            <Button
              variant="secondary"
              onClick={() => navigate('/grados/crear')}
              leftIcon={
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
              }
            >
              Crear Grado
            </Button>
          </div>
        </CardHeader>
        <CardBody>
          {asignatura.grados && asignatura.grados.length > 0 ? (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              {asignatura.grados.map((grado) => (
                <div key={grado.id} className="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                  <div className="flex items-center justify-between">
                    <div>
                      <h4 className="text-sm font-medium text-gray-900">{grado.nombre}</h4>
                      {grado.nivel && (
                        <p className="text-sm text-gray-500">{grado.nivel}</p>
                      )}
                    </div>
                    <Button
                      variant="ghost"
                      size="sm"
                      onClick={() => navigate(`/grados/${grado.id}`)}
                    >
                      Ver
                    </Button>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <div className="text-center py-8">
              <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
              </svg>
              <h3 className="mt-2 text-sm font-medium text-gray-900">No hay grados</h3>
              <p className="mt-1 text-sm text-gray-500">
                Esta asignatura no está asociada a ningún grado.
              </p>
              <div className="mt-6">
                <Button
                  variant="primary"
                  onClick={() => navigate('/grados/crear')}
                  leftIcon={
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                  }
                >
                  Crear Grado
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
              onClick={() => navigate('/asignaturas')}
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
              onClick={() => navigate(`/asignaturas/${id}/editar`)}
              leftIcon={
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              }
            >
              Editar Asignatura
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
              Eliminar Asignatura
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

export default AsignaturaDetailPage; 