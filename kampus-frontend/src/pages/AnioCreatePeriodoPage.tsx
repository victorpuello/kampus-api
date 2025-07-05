import { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import axiosClient from '../api/axiosClient';
import { PeriodoForm } from '../components/periodos/PeriodoForm';
import type { PeriodoFormValues } from '../components/periodos/PeriodoForm';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
import { useAlertContext } from '../contexts/AlertContext';
import PageHeader from '../components/ui/PageHeader';
import { Button } from '../components/ui/Button';

interface Anio {
  id: number;
  nombre: string;
  estado: string;
}

const AnioCreatePeriodoPage = () => {
  const { anioId } = useParams<{ anioId: string }>();
  const navigate = useNavigate();
  const { showSuccess, showError } = useAlertContext();
  
  const [formValues, setFormValues] = useState<PeriodoFormValues>({
    nombre: '',
    fecha_inicio: '',
    fecha_fin: '',
    anio_id: Number(anioId) || 0,
  });
  const [errors, setErrors] = useState<Partial<Record<keyof PeriodoFormValues, string>>>({});
  const [loading, setLoading] = useState(false);
  const [anio, setAnio] = useState<Anio | null>(null);
  const [loadingAnio, setLoadingAnio] = useState(true);

  useEffect(() => {
    const fetchAnio = async () => {
      try {
        setLoadingAnio(true);
        const response = await axiosClient.get(`/anios/${anioId}`);
        const anioData: Anio = response.data.data || response.data;
        setAnio(anioData);
        setFormValues(prev => ({
          ...prev,
          anio_id: anioData.id
        }));
      } catch (error) {
        console.error('Error al cargar año académico:', error);
        showError('Error al cargar el año académico');
      } finally {
        setLoadingAnio(false);
      }
    };

    if (anioId) {
      fetchAnio();
    }
  }, [anioId, showError]);

  const handleSubmit = async (values: PeriodoFormValues) => {
    try {
      setLoading(true);
      setErrors({});

      const response = await axiosClient.post(`/anios/${anioId}/periodos`, values);
      
      showSuccess('Periodo creado exitosamente');
      navigate(`/anios/${anioId}/periodos/${response.data.data?.id || response.data.id}`);
    } catch (err: any) {
      if (err.response?.status === 422) {
        setErrors(err.response.data.errors);
      } else {
        showError(err.response?.data?.message || 'Error al crear el periodo');
      }
    } finally {
      setLoading(false);
    }
  };

  if (loadingAnio) {
    return (
      <div className="flex justify-center items-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900"></div>
      </div>
    );
  }

  if (!anio) {
    return (
      <div className="rounded-md bg-red-50 p-4">
        <div className="flex">
          <svg className="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p className="text-sm text-red-800">No se pudo cargar el año académico</p>
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <PageHeader
        title="Crear Periodo Académico"
        description={`Complete el formulario para crear un nuevo periodo en el año ${anio.nombre}`}
      >
        <Button
          variant="secondary"
          onClick={() => navigate(`/anios/${anioId}/periodos`)}
        >
          Volver a la lista
        </Button>
      </PageHeader>

      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold text-gray-900">
            Información del Periodo
          </h2>
        </CardHeader>
        <CardBody>
          <PeriodoForm
            values={formValues}
            onChange={setFormValues}
            onSubmit={handleSubmit}
            loading={loading}
            errors={errors}
            submitText="Crear Periodo"
            anioId={Number(anioId)}
          />
        </CardBody>
      </Card>
    </div>
  );
};

export default AnioCreatePeriodoPage; 