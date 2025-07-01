import { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import axiosClient from '../api/axiosClient';
import { Button } from '../components/ui/Button';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
import { Badge } from '../components/ui/Badge';
import { useAlertContext } from '../contexts/AlertContext';
import { useConfirm } from '../hooks/useConfirm';
import ConfirmDialog from '../components/ui/ConfirmDialog';

interface Teacher {
  id: number;
  user: {
    nombre: string;
    apellido: string;
    tipo_documento: string;
    numero_documento: string;
    email: string;
  };
  telefono: string;
  especialidad: string;
  estado: string;
  institucion: {
    id: number;
    nombre: string;
  };
  fecha_contratacion?: string;
  salario?: number;
  horario_trabajo?: string;
}

const TeacherDetailPage = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const { showSuccess, showError } = useAlertContext();
  const { dialogState, confirm, setLoading: setConfirmLoading } = useConfirm();
  const [teacher, setTeacher] = useState<Teacher | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchTeacher = async () => {
      try {
        const response = await axiosClient.get(`/docentes/${id}`);
        setTeacher(response.data.data);
        setError(null);
      } catch (err: any) {
        setError(err.response?.data?.message || 'Error al cargar el docente');
      } finally {
        setLoading(false);
      }
    };

    fetchTeacher();
  }, [id]);

  const handleDelete = async () => {
    const confirmed = await confirm({
      title: 'Eliminar Docente',
      message: `¿Estás seguro de que deseas eliminar al docente ${teacher?.user.nombre} ${teacher?.user.apellido}? Esta acción no se puede deshacer.`,
      confirmText: 'Eliminar',
      cancelText: 'Cancelar',
      variant: 'danger'
    });

    if (!confirmed) return;

    try {
      setConfirmLoading(true);
      await axiosClient.delete(`/docentes/${id}`);
      showSuccess('Docente eliminado exitosamente', 'Éxito');
      navigate('/docentes');
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || 'Error al eliminar el docente';
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

  if (!teacher) {
    return (
      <div className="text-center">
        <h3 className="text-lg font-medium text-gray-900">Docente no encontrado</h3>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div className="sm:flex sm:items-center sm:justify-between flex-wrap">
        <div>
          <h1 className="text-2xl font-semibold text-gray-900">
            {teacher.user.nombre} {teacher.user.apellido}
          </h1>
          <p className="mt-2 text-sm text-gray-700">
            {teacher.user.tipo_documento} {teacher.user.numero_documento}
          </p>
        </div>
        <div className="mt-4 sm:mt-0 flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
          <Button
            variant="primary"
            onClick={() => navigate(`/docentes/${id}/editar`)}
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
        {/* Información Personal */}
        <Card>
          <CardHeader>
            <h3 className="text-lg font-semibold text-gray-900">Información Personal</h3>
          </CardHeader>
          <CardBody>
            <div className="space-y-4">
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Nombre Completo:</span>
                <span className="text-sm text-gray-900">
                  {teacher.user.nombre} {teacher.user.apellido}
                </span>
              </div>
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Documento:</span>
                <span className="text-sm text-gray-900">
                  {teacher.user.tipo_documento} {teacher.user.numero_documento}
                </span>
              </div>
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Email:</span>
                <span className="text-sm text-gray-900">{teacher.user.email}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Teléfono:</span>
                <span className="text-sm text-gray-900">{teacher.telefono}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Estado:</span>
                <Badge
                  variant={teacher.estado === 'activo' ? 'success' : 'error'}
                >
                  {teacher.estado}
                </Badge>
              </div>
            </div>
          </CardBody>
        </Card>

        {/* Información Profesional */}
        <Card>
          <CardHeader>
            <h3 className="text-lg font-semibold text-gray-900">Información Profesional</h3>
          </CardHeader>
          <CardBody>
            <div className="space-y-4">
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Especialidad:</span>
                <span className="text-sm text-gray-900">{teacher.especialidad}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Institución:</span>
                <span className="text-sm text-gray-900">{teacher.institucion.nombre}</span>
              </div>
              {teacher.fecha_contratacion && (
                <div className="flex justify-between">
                  <span className="text-sm font-medium text-gray-500">Fecha de Contratación:</span>
                  <span className="text-sm text-gray-900">
                    {new Date(teacher.fecha_contratacion).toLocaleDateString()}
                  </span>
                </div>
              )}
              {teacher.salario && (
                <div className="flex justify-between">
                  <span className="text-sm font-medium text-gray-500">Salario:</span>
                  <span className="text-sm text-gray-900">
                    ${teacher.salario.toLocaleString()}
                  </span>
                </div>
              )}
              {teacher.horario_trabajo && (
                <div className="flex justify-between">
                  <span className="text-sm font-medium text-gray-500">Horario de Trabajo:</span>
                  <span className="text-sm text-gray-900">{teacher.horario_trabajo}</span>
                </div>
              )}
            </div>
          </CardBody>
        </Card>
      </div>

      {/* Acciones adicionales */}
      <Card>
        <CardHeader>
          <h3 className="text-lg font-semibold text-gray-900">Acciones</h3>
        </CardHeader>
        <CardBody>
          <div className="flex flex-wrap gap-3">
            <Button
              variant="secondary"
              onClick={() => navigate('/docentes')}
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
              onClick={() => navigate(`/docentes/${id}/editar`)}
              leftIcon={
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              }
            >
              Editar Docente
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
              Eliminar Docente
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

export default TeacherDetailPage; 