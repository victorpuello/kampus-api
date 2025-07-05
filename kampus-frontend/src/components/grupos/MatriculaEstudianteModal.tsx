import React, { useEffect, useState } from 'react';
import axiosClient from '../../api/axiosClient';
import { Button } from '../ui/Button';
import { useAlertContext } from '../../contexts/AlertContext';

interface Estudiante {
  id: number;
  nombre?: string;
  apellido?: string;
  email?: string;
  estado: string;
  grupo_id?: number;
  codigo_estudiantil?: string;
  user?: {
    nombre: string;
    apellido: string;
    email?: string;
  };
}

interface MatriculaEstudianteModalProps {
  isOpen: boolean;
  onClose: () => void;
  grupoId: number;
  onEstudianteMatriculado: () => void;
}

export const MatriculaEstudianteModal: React.FC<MatriculaEstudianteModalProps> = ({
  isOpen,
  onClose,
  grupoId,
  onEstudianteMatriculado
}) => {
  const { showSuccess, showError } = useAlertContext();
  const [estudiantes, setEstudiantes] = useState<Estudiante[]>([]);
  const [filteredEstudiantes, setFilteredEstudiantes] = useState<Estudiante[]>([]);
  const [loading, setLoading] = useState(false);
  const [matriculando, setMatriculando] = useState(false);
  const [selectedEstudiante, setSelectedEstudiante] = useState<Estudiante | null>(null);
  const [searchTerm, setSearchTerm] = useState('');

  useEffect(() => {
    if (isOpen) {
      fetchEstudiantesDisponibles();
    }
  }, [isOpen, grupoId]);

  useEffect(() => {
    // Filtrar estudiantes basado en el término de búsqueda
    if (searchTerm.trim() === '') {
      setFilteredEstudiantes(estudiantes);
    } else {
      const filtered = estudiantes.filter(estudiante => {
        const nombre = estudiante.user?.nombre || estudiante.nombre || '';
        const apellido = estudiante.user?.apellido || estudiante.apellido || '';
        const email = estudiante.user?.email || estudiante.email || '';
        const codigo = estudiante.codigo_estudiantil || '';
        
        return nombre.toLowerCase().includes(searchTerm.toLowerCase()) ||
               apellido.toLowerCase().includes(searchTerm.toLowerCase()) ||
               email.toLowerCase().includes(searchTerm.toLowerCase()) ||
               codigo.toLowerCase().includes(searchTerm.toLowerCase());
      });
      setFilteredEstudiantes(filtered);
    }
  }, [estudiantes, searchTerm]);

  const fetchEstudiantesDisponibles = async () => {
    try {
      setLoading(true);
      const response = await axiosClient.get('/estudiantes?sin_grupo=true&per_page=100');
      setEstudiantes(response.data.data || response.data);
    } catch (error) {
      console.error('Error al cargar estudiantes:', error);
      showError('Error al cargar estudiantes disponibles', 'Error');
    } finally {
      setLoading(false);
    }
  };

  const handleMatricular = async () => {
    if (!selectedEstudiante) {
      showError('Por favor selecciona un estudiante', 'Error');
      return;
    }

    try {
      setMatriculando(true);
      await axiosClient.put(`/estudiantes/${selectedEstudiante.id}`, {
        grupo_id: grupoId
      });
      
      showSuccess('Estudiante matriculado exitosamente', 'Éxito');
      onEstudianteMatriculado();
      onClose();
      setSelectedEstudiante(null);
      setSearchTerm('');
    } catch (error: any) {
      console.error('Error al matricular estudiante:', error);
      const errorMessage = error.response?.data?.message || 'Error al matricular estudiante';
      showError(errorMessage, 'Error');
    } finally {
      setMatriculando(false);
    }
  };

  const handleEstudianteSelect = (estudiante: Estudiante) => {
    setSelectedEstudiante(estudiante);
  };

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div className="relative top-10 mx-auto p-6 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div className="mt-3">
          <div className="flex items-center justify-between mb-6">
            <h3 className="text-xl font-semibold text-gray-900">
              Matricular Estudiante
            </h3>
            <button
              onClick={onClose}
              className="text-gray-400 hover:text-gray-600 transition-colors"
            >
              <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          {loading ? (
            <div className="text-center py-8">
              <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900 mx-auto"></div>
              <p className="mt-4 text-sm text-gray-600">Cargando estudiantes disponibles...</p>
            </div>
          ) : estudiantes.length === 0 ? (
            <div className="text-center py-8">
              <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
              </svg>
              <h3 className="mt-2 text-sm font-medium text-gray-900">No hay estudiantes disponibles</h3>
              <p className="mt-1 text-sm text-gray-500">
                Todos los estudiantes ya están matriculados en grupos.
              </p>
            </div>
          ) : (
            <>
              {/* Buscador */}
              <div className="mb-6">
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Buscar Estudiante
                </label>
                <div className="relative">
                  <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg className="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                  </div>
                  <input
                    type="text"
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    placeholder="Buscar por nombre, apellido, email o código estudiantil..."
                    className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                  />
                </div>
                <p className="mt-1 text-sm text-gray-500">
                  {filteredEstudiantes.length} de {estudiantes.length} estudiantes encontrados
                </p>
              </div>

              {/* Lista de Estudiantes */}
              <div className="mb-6">
                <label className="block text-sm font-medium text-gray-700 mb-3">
                  Estudiantes Disponibles
                </label>
                <div className="max-h-64 overflow-y-auto border border-gray-200 rounded-lg">
                  {filteredEstudiantes.length === 0 ? (
                    <div className="text-center py-8">
                      <p className="text-sm text-gray-500">No se encontraron estudiantes con ese criterio de búsqueda</p>
                    </div>
                  ) : (
                    <div className="divide-y divide-gray-200">
                      {filteredEstudiantes.map((estudiante) => (
                        <div
                          key={estudiante.id}
                          onClick={() => handleEstudianteSelect(estudiante)}
                          className={`p-4 cursor-pointer transition-colors hover:bg-gray-50 ${
                            selectedEstudiante?.id === estudiante.id 
                              ? 'bg-primary-50 border-l-4 border-primary-500' 
                              : ''
                          }`}
                        >
                          <div className="flex items-center justify-between">
                            <div className="flex items-center space-x-3">
                                                             <div className="flex-shrink-0 h-10 w-10">
                                 <div className="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                   <span className="text-sm font-medium text-gray-700">
                                     {(estudiante.user?.nombre || estudiante.nombre || '').charAt(0)}
                                     {(estudiante.user?.apellido || estudiante.apellido || '').charAt(0)}
                                   </span>
                                 </div>
                               </div>
                               <div>
                                 <div className="text-sm font-medium text-gray-900">
                                   {estudiante.user?.nombre || estudiante.nombre || 'Sin nombre'} {estudiante.user?.apellido || estudiante.apellido || 'Sin apellido'}
                                 </div>
                                 <div className="text-sm text-gray-500">
                                   {estudiante.user?.email || estudiante.email || 'Sin email'}
                                   {estudiante.codigo_estudiantil && ` • ${estudiante.codigo_estudiantil}`}
                                 </div>
                               </div>
                            </div>
                            {selectedEstudiante?.id === estudiante.id && (
                              <div className="flex-shrink-0">
                                <svg className="h-5 w-5 text-primary-500" fill="currentColor" viewBox="0 0 20 20">
                                  <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
                                </svg>
                              </div>
                            )}
                          </div>
                        </div>
                      ))}
                    </div>
                  )}
                </div>
              </div>

              {/* Estudiante Seleccionado */}
              {selectedEstudiante && (
                <div className="mb-6 p-4 bg-primary-50 border border-primary-200 rounded-lg">
                  <div className="flex items-center justify-between">
                                         <div>
                       <h4 className="text-sm font-medium text-primary-900">
                         Estudiante Seleccionado
                       </h4>
                       <p className="text-sm text-primary-700">
                         {selectedEstudiante.user?.nombre || selectedEstudiante.nombre || 'Sin nombre'} {selectedEstudiante.user?.apellido || selectedEstudiante.apellido || 'Sin apellido'}
                         {selectedEstudiante.codigo_estudiantil && ` (${selectedEstudiante.codigo_estudiantil})`}
                       </p>
                     </div>
                    <button
                      onClick={() => setSelectedEstudiante(null)}
                      className="text-primary-600 hover:text-primary-800"
                    >
                      <svg className="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                      </svg>
                    </button>
                  </div>
                </div>
              )}

              {/* Botones de Acción */}
              <div className="flex justify-end space-x-3">
                <Button
                  variant="secondary"
                  onClick={onClose}
                  disabled={matriculando}
                >
                  Cancelar
                </Button>
                <Button
                  variant="primary"
                  onClick={handleMatricular}
                  loading={matriculando}
                  disabled={!selectedEstudiante || matriculando}
                >
                  Matricular Estudiante
                </Button>
              </div>
            </>
          )}
        </div>
      </div>
    </div>
  );
}; 