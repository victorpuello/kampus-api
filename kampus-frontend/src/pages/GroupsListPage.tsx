import { useEffect, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import axiosClient from '../api/axiosClient';
import { Button } from '../components/ui/Button';
import { DataTable } from '../components/ui/DataTable';
import type { Column, ActionButton } from '../components/ui/DataTable';
import { useAlertContext } from '../contexts/AlertContext';
import { useConfirm } from '../hooks/useConfirm';
import ConfirmDialog from '../components/ui/ConfirmDialog';

interface Grupo {
  id: number;
  nombre: string;
  descripcion?: string;
  sede_id: number;
  sede?: {
    id: number;
    nombre: string;
    institucion: {
      id: number;
      nombre: string;
    };
  };
  grado_id: number;
  grado?: {
    id: number;
    nombre: string;
    nivel?: string;
  };
  anio_id: number;
  anio?: {
    id: number;
    nombre: string;
    estado: string;
  };
  director_docente_id?: number;
  director_docente?: {
    id: number;
    nombre: string;
    apellido: string;
  };
  capacidad_maxima?: number;
  estado: string;
  estudiantes_count?: number;
}

const GroupsListPage = () => {
  const navigate = useNavigate();
  const { showSuccess, showError } = useAlertContext();
  const { dialogState, confirm, setLoading: setConfirmLoading } = useConfirm();
  const [grupos, setGrupos] = useState<Grupo[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const fetchGrupos = async () => {
    try {
      setLoading(true);
      const response = await axiosClient.get('/grupos');
      console.log('Datos de grupos recibidos:', response.data);
      setGrupos(response.data.data || response.data);
      setError(null);
    } catch (err: any) {
      setError(err.response?.data?.message || 'Error al cargar los grupos');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchGrupos();
  }, []);

  const handleDelete = async (grupo: Grupo) => {
    const confirmed = await confirm({
      title: 'Eliminar Grupo',
      message: `¿Estás seguro de que deseas eliminar el grupo "${grupo.nombre}"? Esta acción no se puede deshacer.`,
      confirmText: 'Eliminar',
      cancelText: 'Cancelar',
      variant: 'danger'
    });

    if (!confirmed) return;

    try {
      setConfirmLoading(true);
      await axiosClient.delete(`/grupos/${grupo.id}`);
      showSuccess('Grupo eliminado exitosamente', 'Éxito');
      fetchGrupos();
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || 'Error al eliminar el grupo';
      showError(errorMessage, 'Error');
      setError(errorMessage);
    } finally {
      setConfirmLoading(false);
    }
  };

  const handleBulkDelete = async (selectedGrupos: Grupo[]) => {
    const confirmed = await confirm({
      title: 'Eliminar Grupos',
      message: `¿Estás seguro de que deseas eliminar ${selectedGrupos.length} grupos? Esta acción no se puede deshacer.`,
      confirmText: 'Eliminar',
      cancelText: 'Cancelar',
      variant: 'danger'
    });

    if (!confirmed) return;

    try {
      setConfirmLoading(true);
      await Promise.all(selectedGrupos.map(grupo => 
        axiosClient.delete(`/grupos/${grupo.id}`)
      ));
      showSuccess(`${selectedGrupos.length} grupos eliminados exitosamente`, 'Éxito');
      fetchGrupos();
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || 'Error al eliminar los grupos';
      showError(errorMessage, 'Error');
      setError(errorMessage);
    } finally {
      setConfirmLoading(false);
    }
  };

  // Definir las columnas de la tabla
  const columns: Column<Grupo>[] = [
    {
      key: 'nombre',
      header: 'Grupo',
      accessor: (grupo) => (
        <div className="flex items-center">
          <div className="flex-shrink-0 h-10 w-10">
            <div className="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
              <span className="text-sm font-medium text-blue-700">
                {grupo.nombre.charAt(0)}
              </span>
            </div>
          </div>
          <div className="ml-4">
            <div className="text-sm font-medium text-gray-900">
              {grupo.nombre}
            </div>
            {grupo.grado && (
              <div className="text-sm text-gray-500">
                {grupo.grado.nombre} {grupo.grado.nivel && `(${grupo.grado.nivel})`}
              </div>
            )}
          </div>
        </div>
      ),
      sortable: true,
    },
    {
      key: 'sede',
      header: 'Sede',
      accessor: (grupo) => (
        <div className="text-sm">
          <div className="font-medium text-gray-900">
            {grupo.sede?.nombre || 'Sin sede'}
          </div>
          {grupo.sede?.institucion && (
            <div className="text-gray-500">
              {grupo.sede.institucion.nombre}
            </div>
          )}
        </div>
      ),
      sortable: true,
    },
    {
      key: 'anio',
      header: 'Año Académico',
      accessor: (grupo) => (
        <div className="text-sm">
          <div className="font-medium text-gray-900">
            {grupo.anio?.nombre || 'Sin año'}
          </div>
          {grupo.anio && (
            <span
              className={`inline-flex rounded-full px-2 text-xs font-semibold leading-5 ${
                grupo.anio.estado === 'activo'
                  ? 'bg-green-100 text-green-800'
                  : 'bg-gray-100 text-gray-800'
              }`}
            >
              {grupo.anio.estado}
            </span>
          )}
        </div>
      ),
      sortable: true,
    },
    {
      key: 'director_docente',
      header: 'Director',
      accessor: (grupo) => (
        <div className="text-sm text-gray-900">
          {grupo.director_docente ? (
            `${grupo.director_docente.nombre} ${grupo.director_docente.apellido}`
          ) : (
            <span className="text-gray-500">Sin asignar</span>
          )}
        </div>
      ),
      sortable: true,
    },
    {
      key: 'estudiantes_count',
      header: 'Estudiantes',
      accessor: (grupo) => (
        <div className="text-sm text-gray-500">
          <span>{grupo.estudiantes_count || 0}</span>
          {grupo.capacidad_maxima && (
            <span className="text-gray-400"> / {grupo.capacidad_maxima}</span>
          )}
        </div>
      ),
      sortable: true,
      align: 'center',
    },
    {
      key: 'estado',
      header: 'Estado',
      accessor: (grupo) => (
        <span
          className={`inline-flex rounded-full px-2 text-xs font-semibold leading-5 ${
            grupo.estado === 'activo'
              ? 'bg-green-100 text-green-800'
              : 'bg-red-100 text-red-800'
          }`}
        >
          {grupo.estado}
        </span>
      ),
      sortable: true,
      align: 'center',
    },
  ];

  // Definir las acciones de la tabla
  const actions: ActionButton<Grupo>[] = [
    {
      label: 'Ver',
      variant: 'ghost',
      size: 'sm',
      onClick: (grupo) => navigate(`/grupos/${grupo.id}`),
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
      onClick: (grupo) => navigate(`/grupos/${grupo.id}/editar`),
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
  const bulkActions: ActionButton<Grupo[]>[] = [
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
          <h1 className="text-2xl font-bold text-gray-900">Grupos</h1>
          <p className="text-gray-600 mt-1">
            Gestiona todos los grupos académicos del sistema
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
          <Link to="/grupos/crear">Agregar Grupo</Link>
        </Button>
      </div>

      {/* DataTable */}
      <DataTable
        data={grupos}
        columns={columns}
        actions={actions}
        loading={loading}
        error={error}
        searchable={true}
        searchPlaceholder="Buscar grupos por nombre, descripción o grado..."
        searchKeys={['nombre', 'descripcion', 'grado.nombre', 'grado.nivel', 'sede.nombre', 'sede.institucion.nombre', 'anio.nombre', 'director_docente.nombre', 'director_docente.apellido', 'estado']}
        sortable={true}
        pagination={true}
        itemsPerPage={10}
        itemsPerPageOptions={[5, 10, 25, 50]}
        emptyMessage="No hay grupos registrados"
        emptyIcon={
          <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
        }
        selectable={true}
        bulkActions={bulkActions}
        onRowClick={(grupo) => navigate(`/grupos/${grupo.id}`)}
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

export default GroupsListPage; 