import { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import axiosClient from '../api/axiosClient';
import { PeriodoForm } from '../components/periodos/PeriodoForm';
import type { PeriodoFormValues } from '../components/periodos/PeriodoForm';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
import { useAlertContext } from '../contexts/AlertContext';
import PageHeader from '../components/ui/PageHeader';
import { Button } from '../components/ui/Button';

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
  };
}

const AnioEditPeriodoPage = () => {
  const { anioId, periodoId } = useParams<{ anioId: string; periodoId: string }>();
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
  const [loadingData, setLoadingData] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchPeriodo = async () => {
      try {
        setLoadingData(true);
        const response = await axiosClient.get(`/anios/${anioId}/periodos/${periodoId}`);
        const periodo: Periodo = response.data.data || response.data;
        
        setFormValues({
          nombre: periodo.nombre,
          fecha_inicio: periodo.fecha_inicio,
          fecha_fin: periodo.fecha_fin,
          anio_id: periodo.anio_id,
        });
        setError(null);
      } catch (err: any) {
        setError(err.response?.data?.message || 'Error al cargar el periodo');
      } finally {
        setLoadingData(false);
      }
    };
    if (anioId && periodoId) {
      fetchPeriodo();
    }
  }, [anioId, periodoId]);

  const handleSubmit = async (values: PeriodoFormValues) => {
    try {
      setLoading(true);
      setErrors({});

      await axiosClient.put(`/anios/${anioId}/periodos/${periodoId}`, values);
      
      showSuccess('Periodo actualizado exitosamente');
      navigate(`/anios/${anioId}/periodos/${periodoId}`);
    } catch (err: any) {
      if (err.response?.status === 422) {
        setErrors(err.response.data.errors);
      } else {
        showError(err.response?.data?.message || 'Error al actualizar el periodo');
      }
    } finally {
      setLoading(false);
    }
  };

  if (loadingData) {
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

  return (
    <div className="space-y-6">
      <PageHeader
        title="Editar Periodo Académico"
        description="Modifica la información del periodo académico"
      >
        <Button
          variant="secondary"
          onClick={() => navigate(`/anios/${anioId}/periodos/${periodoId}`)}
        >
          Volver al detalle
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
            submitText="Actualizar Periodo"
            anioId={Number(anioId)}
          />
        </CardBody>
      </Card>
    </div>
  );
};

export default AnioEditPeriodoPage; 