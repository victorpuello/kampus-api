import { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import axiosClient from '../api/axiosClient';
import { Button } from '../components/ui/Button';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
import { useAlertContext } from '../contexts/AlertContext';
import { useConfirm } from '../hooks/useConfirm';
import PageHeader from '../components/ui/PageHeader';

interface Periodo {
  id: number;
  nombre: string;
  fecha_inicio: string;
  fecha_fin: string;
  anio_id: number;
  anio?: {
    id: number;
    nombre: string;
    estado: string;
    institucion?: {
      id: number;
      nombre: string;
      siglas: string;
    };
  };
}

const AnioPeriodoDetailPage = () => {
  const { anioId, periodoId } = useParams<{ anioId: string; periodoId: string }>();
  const navigate = useNavigate();
  const { showSuccess, showError } = useAlertContext();
  const { confirm } = useConfirm();
  
  const [periodo, setPeriodo] = useState<Periodo | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchPeriodo = async () => {
      try {
        setLoading(true);
        const response = await axiosClient.get(`/anios/${anioId}/periodos/${periodoId}`);
        setPeriodo(response.data.data || response.data);
        setError(null);
      } catch (err: any) {
        setError(err.response?.data?.message || 'Error al cargar el periodo');
      } finally {
        setLoading(false);
      }
    };
    if (anioId && periodoId) {
      fetchPeriodo();
    }
  }, [anioId, periodoId]);

  const handleDelete = async () => {
    const confirmed = await confirm({
      title: 'Eliminar Periodo',
      message: `¿Estás seguro de que quieres eliminar el periodo "${periodo?.nombre}"?`,
      variant: 'danger',
    });

    if (confirmed && periodo) {
      try {
        await axiosClient.delete(`/anios/${anioId}/periodos/${periodo.id}`);
        showSuccess('Periodo eliminado exitosamente');
        navigate(`/anios/${anioId}/periodos`);
      } catch (err: any) {
        showError('Error al eliminar el periodo');
      }
    }
  };

  const handleEdit = () => {
    navigate(`/anios/${anioId}/periodos/${periodo?.id}/editar`);
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
          <svg className="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p className="text-sm text-red-800">{error}</p>
        </div>
      </div>
    );
  }

  if (!periodo) return null;

  // Calcular duración del periodo
  const inicio = new Date(periodo.fecha_inicio);
  const fin = new Date(periodo.fecha_fin);
  const diffTime = Math.abs(fin.getTime() - inicio.getTime());
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

  return (
    <div className="space-y-6">
      <PageHeader
        title={periodo.nombre}
        description="Detalle del Periodo Académico"
      >
        <div className="flex space-x-3">
          <Button 
            variant="primary" 
            onClick={handleEdit}
          >
            Editar Periodo
          </Button>
          <Button 
            variant="danger" 
            onClick={handleDelete}
          >
            Eliminar Periodo
          </Button>
          <Button 
            variant="secondary" 
            onClick={() => navigate(`/anios/${anioId}/periodos`)}
          >
            Volver a la lista
          </Button>
        </div>
      </PageHeader>
      
      {/* Información del Periodo */}
      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold text-gray-900">Información del Periodo</h2>
        </CardHeader>
        <CardBody>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div className="space-y-3">
              <div>
                <span className="font-medium text-gray-700">Nombre:</span>
                <p className="text-gray-900">{periodo.nombre}</p>
              </div>
              <div>
                <span className="font-medium text-gray-700">Fecha de Inicio:</span>
                <p className="text-gray-900">{new Date(periodo.fecha_inicio).toLocaleDateString('es-ES')}</p>
              </div>
              <div>
                <span className="font-medium text-gray-700">Fecha de Fin:</span>
                <p className="text-gray-900">{new Date(periodo.fecha_fin).toLocaleDateString('es-ES')}</p>
              </div>
            </div>
            
            <div className="space-y-3">
              <div>
                <span className="font-medium text-gray-700">Duración:</span>
                <p className="text-gray-900">{diffDays} días</p>
              </div>
              {periodo.anio && (
                <div>
                  <span className="font-medium text-gray-700">Año Académico:</span>
                  <p className="text-gray-900">{periodo.anio.nombre}</p>
                  <span className={`inline-flex rounded-full px-2 text-xs font-semibold leading-5 mt-1 ${
                    periodo.anio.estado === 'activo' 
                      ? 'bg-green-100 text-green-800' 
                      : 'bg-red-100 text-red-800'
                  }`}>
                    {periodo.anio.estado}
                  </span>
                </div>
              )}
              {periodo.anio?.institucion && (
                <div>
                  <span className="font-medium text-gray-700">Institución:</span>
                  <p className="text-gray-900">{periodo.anio.institucion.nombre}</p>
                  <p className="text-sm text-gray-500">({periodo.anio.institucion.siglas})</p>
                </div>
              )}
            </div>
          </div>
        </CardBody>
      </Card>

      {/* Navegación al año académico */}
      {periodo.anio && (
        <Card>
          <CardHeader>
            <h2 className="text-lg font-semibold text-gray-900">Año Académico Asociado</h2>
          </CardHeader>
          <CardBody>
            <div className="flex items-center justify-between">
              <div>
                <p className="text-gray-900 font-medium">{periodo.anio.nombre}</p>
                <p className="text-sm text-gray-500">Año académico al que pertenece este periodo</p>
              </div>
              <Button
                variant="secondary"
                onClick={() => navigate(`/anios/${periodo.anio?.id}`)}
              >
                Ver Año Académico
              </Button>
            </div>
          </CardBody>
        </Card>
      )}
    </div>
  );
};

export default AnioPeriodoDetailPage; 