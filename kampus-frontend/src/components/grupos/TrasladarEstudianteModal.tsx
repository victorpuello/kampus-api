import { useState, useEffect } from 'react';
import axiosClient from '../../api/axiosClient';
import { Button } from '../ui/Button';
import { useAlertContext } from '../../contexts/AlertContext';

interface Grupo {
  id: number;
  nombre: string;
  grado: {
    id: number;
    nombre: string;
    nivel?: string;
  };
  sede?: {
    id: number;
    nombre: string;
  };
}

interface TrasladarEstudianteModalProps {
  isOpen: boolean;
  onClose: () => void;
  grupoOrigenId: number;
  estudianteId: number;
  estudianteNombre: string;
  onEstudianteTrasladado: () => void;
}

const TrasladarEstudianteModal = ({
  isOpen,
  onClose,
  grupoOrigenId,
  estudianteId,
  estudianteNombre,
  onEstudianteTrasladado
}: TrasladarEstudianteModalProps) => {
  const [grupos, setGrupos] = useState<Grupo[]>([]);
  const [grupoDestinoId, setGrupoDestinoId] = useState<number | ''>('');
  const [loading, setLoading] = useState(false);
  const [loadingGrupos, setLoadingGrupos] = useState(false);
  const { showSuccess, showError } = useAlertContext();

  const fetchGrupos = async () => {
    try {
      setLoadingGrupos(true);
      const response = await axiosClient.get('/grupos?per_page=100');
      // Filtrar el grupo actual y ordenar por nombre
      const gruposDisponibles = response.data.data.filter((grupo: Grupo) => grupo.id !== grupoOrigenId);
      setGrupos(gruposDisponibles);
    } catch (err: any) {
      showError('Error al cargar los grupos disponibles', 'Error');
    } finally {
      setLoadingGrupos(false);
    }
  };

  useEffect(() => {
    if (isOpen) {
      fetchGrupos();
      setGrupoDestinoId('');
    }
  }, [isOpen, grupoOrigenId]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!grupoDestinoId) {
      showError('Debes seleccionar un grupo destino', 'Error');
      return;
    }

    try {
      setLoading(true);
      await axiosClient.put(`/grupos/${grupoOrigenId}/estudiantes/${estudianteId}/trasladar`, {
        grupo_destino_id: grupoDestinoId
      });
      
      showSuccess('Estudiante trasladado exitosamente', 'Éxito');
      onEstudianteTrasladado();
      onClose();
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || 'Error al trasladar el estudiante';
      showError(errorMessage, 'Error');
    } finally {
      setLoading(false);
    }
  };

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div className="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div className="mt-3">
          <h3 className="text-lg font-medium text-gray-900 mb-4">
            Trasladar Estudiante
          </h3>
          
          <div className="mb-4">
            <p className="text-sm text-gray-600">
              Estudiante: <span className="font-medium">{estudianteNombre}</span>
            </p>
          </div>

          <form onSubmit={handleSubmit}>
            <div className="mb-4">
              <label htmlFor="grupo_destino" className="block text-sm font-medium text-gray-700 mb-2">
                Grupo Destino
              </label>
              <select
                id="grupo_destino"
                value={grupoDestinoId}
                onChange={(e) => setGrupoDestinoId(e.target.value ? Number(e.target.value) : '')}
                className="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                disabled={loadingGrupos}
                required
              >
                <option value="">Selecciona un grupo destino</option>
                {grupos.map((grupo) => (
                  <option key={grupo.id} value={grupo.id}>
                    {grupo.sede?.nombre || 'Sin sede'} - {grupo.grado.nombre} - {grupo.nombre}
                  </option>
                ))}
              </select>
              {loadingGrupos && (
                <p className="text-sm text-gray-500 mt-1">Cargando grupos...</p>
              )}
            </div>

            <div className="flex justify-end space-x-3">
              <Button
                type="button"
                variant="secondary"
                onClick={onClose}
                disabled={loading}
              >
                Cancelar
              </Button>
              <Button
                type="submit"
                variant="primary"
                disabled={loading || !grupoDestinoId}
              >
                {loading ? 'Trasladando...' : 'Trasladar'}
              </Button>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
};

export default TrasladarEstudianteModal; 