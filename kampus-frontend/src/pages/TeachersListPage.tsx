import { useEffect, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import axiosClient from '../api/axiosClient';
import { Button } from '../components/ui/Button';
import { DataTable } from '../components/ui/DataTable';
import type { Column, ActionButton } from '../components/ui/DataTable';
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
}

const TeachersListPage = () => {
  const navigate = useNavigate();
  const { showSuccess, showError } = useAlertContext();
  const { dialogState, confirm, setLoading: setConfirmLoading } = useConfirm();
  const [teachers, setTeachers] = useState<Teacher[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const fetchTeachers = async () => {
    try {
      setLoading(true);
      const response = await axiosClient.get('/docentes');
      setTeachers(response.data.data);
      setError(null);
    } catch (err: any) {
      setError(err.response?.data?.message || 'Error al cargar los docentes');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchTeachers();
  }, []);

  const handleDelete = async (teacher: Teacher) => {
    const confirmed = await confirm({
      title: 'Eliminar Docente',
      message: `¿Estás seguro de que deseas eliminar al docente ${teacher.user.nombre} ${teacher.user.apellido}? Esta acción no se puede deshacer.`,
      confirmText: 'Eliminar',
      cancelText: 'Cancelar',
      variant: 'danger'
    });

    if (!confirmed) return;

    try {
      setConfirmLoading(true);
      await axiosClient.delete(`/docentes/${teacher.id}`);
      showSuccess('Docente eliminado exitosamente', 'Éxito');
      fetchTeachers();
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || 'Error al eliminar el docente';
      showError(errorMessage, 'Error');
      setError(errorMessage);
    } finally {
      setConfirmLoading(false);
    }
  };

  const handleBulkDelete = async (selectedTeachers: Teacher[]) => {
    const confirmed = await confirm({
      title: 'Eliminar Docentes',
      message: `¿Estás seguro de que deseas eliminar ${selectedTeachers.length} docentes? Esta acción no se puede deshacer.`,
      confirmText: 'Eliminar',
      cancelText: 'Cancelar',
      variant: 'danger'
    });

    if (!confirmed) return;

    try {
      setConfirmLoading(true);
      await Promise.all(selectedTeachers.map(teacher => 
        axiosClient.delete(`/docentes/${teacher.id}`)
      ));
      showSuccess(`${selectedTeachers.length} docentes eliminados exitosamente`, 'Éxito');
      fetchTeachers();
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || 'Error al eliminar los docentes';
      showError(errorMessage, 'Error');
      setError(errorMessage);
    } finally {
      setConfirmLoading(false);
    }
  };

  // Definir las columnas de la tabla
  const columns: Column<Teacher>[] = [
    {
      key: 'nombre',
      header: 'Docente',
      accessor: (teacher) => (
        <div className="flex items-center">
          <div className="flex-shrink-0 h-10 w-10">
            <div className="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
              <span className="text-sm font-medium text-blue-700">
                {(teacher.user?.nombre?.charAt(0) ?? '')}{(teacher.user?.apellido?.charAt(0) ?? '')}
              </span>
            </div>
          </div>
          <div className="ml-4">
            <div className="text-sm font-medium text-gray-900">
              {teacher.user?.nombre} {teacher.user?.apellido}
            </div>
            <div className="text-sm text-gray-500">{teacher.especialidad}</div>
          </div>
        </div>
      ),
      sortable: true,
    },
    {
      key: 'documento',
      header: 'Documento',
      accessor: (teacher) => (
        <span className="text-sm text-gray-500">
          {teacher.user?.tipo_documento} {teacher.user?.numero_documento}
        </span>
      ),
      sortable: true,
    },
    {
      key: 'email',
      header: 'Email',
      accessor: (teacher) => (
        <span className="text-sm text-gray-500">{teacher.user?.email}</span>
      ),
      sortable: true,
    },
    {
      key: 'telefono',
      header: 'Teléfono',
      accessor: (teacher) => (
        <span className="text-sm text-gray-500">{teacher.telefono}</span>
      ),
      sortable: true,
    },
    {
      key: 'institucion',
      header: 'Institución',
      accessor: (teacher) => (
        <span className="text-sm text-gray-500">{teacher.institucion?.nombre}</span>
      ),
      sortable: true,
    },
    {
      key: 'estado',
      header: 'Estado',
      accessor: (teacher) => (
        <span
          className={`inline-flex rounded-full px-2 text-xs font-semibold leading-5 ${
            teacher.estado === 'activo'
              ? 'bg-green-100 text-green-800'
              : 'bg-red-100 text-red-800'
          }`}
        >
          {teacher.estado}
        </span>
      ),
      sortable: true,
      align: 'center',
    },
  ];

  // Definir las acciones de la tabla
  const actions: ActionButton<Teacher>[] = [
    {
      label: 'Ver',
      variant: 'ghost',
      size: 'sm',
      onClick: (teacher) => navigate(`/docentes/${teacher.id}`),
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
      onClick: (teacher) => navigate(`/docentes/${teacher.id}/editar`),
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

  // Acciones en lote
  const bulkActions: ActionButton<Teacher[]>[] = [
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
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Docentes</h1>
          <p className="text-gray-600 mt-1">
            Gestiona todos los docentes registrados en el sistema
          </p>
        </div>
        <Button
          asChild
          leftIcon={
            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
          }
        >
          <Link to="/docentes/crear">Agregar Docente</Link>
        </Button>
      </div>

      {/* DataTable */}
      <DataTable
        data={teachers}
        columns={columns}
        actions={actions}
        loading={loading}
        error={error}
        searchable={true}
        searchPlaceholder="Buscar docentes por nombre, documento o email..."
        searchKeys={['user.nombre', 'user.apellido', 'user.email', 'user.numero_documento', 'telefono', 'especialidad', 'institucion.nombre', 'estado']}
        sortable={true}
        pagination={true}
        itemsPerPage={10}
        itemsPerPageOptions={[5, 10, 25, 50]}
        emptyMessage="No hay docentes registrados"
        emptyIcon={
          <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        }
        selectable={true}
        bulkActions={bulkActions}
        onRowClick={(teacher) => navigate(`/docentes/${teacher.id}`)}
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

export default TeachersListPage; 