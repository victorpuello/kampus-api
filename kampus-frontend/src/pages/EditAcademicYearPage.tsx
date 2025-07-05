import { useState, useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import axiosClient from '../api/axiosClient';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
import { AnioForm } from '../components/anios/AnioForm';
import type { AnioFormValues } from '../components/anios/AnioForm';
import { useAlertContext } from '../contexts/AlertContext';

const EditAcademicYearPage = () => {
  const navigate = useNavigate();
  const { id } = useParams<{ id: string }>();
  const { showSuccess, showError } = useAlertContext();
  const [loading, setLoading] = useState(false);
  const [loadingData, setLoadingData] = useState(true);
  const [errors, setErrors] = useState<Partial<Record<keyof AnioFormValues, string>>>({});

  const initialValues: AnioFormValues = {
    nombre: '',
    fecha_inicio: '',
    fecha_fin: '',
    institucion_id: 0,
    estado: 'activo',
  };

  const [values, setValues] = useState<AnioFormValues>(initialValues);

  useEffect(() => {
    const fetchAnio = async () => {
      try {
        setLoadingData(true);
        const response = await axiosClient.get(`/anios/${id}`);
        const anio = response.data.data || response.data;
        setValues({
          nombre: anio.nombre,
          fecha_inicio: anio.fecha_inicio,
          fecha_fin: anio.fecha_fin,
          institucion_id: anio.institucion_id,
          estado: anio.estado,
        });
      } catch (err: any) {
        showError(err.response?.data?.message || 'Error al cargar el año académico', 'Error');
        navigate('/anios');
      } finally {
        setLoadingData(false);
      }
    };
    if (id) {
      fetchAnio();
    }
  }, [id, navigate, showError]);

  const handleSubmit = async (formValues: AnioFormValues) => {
    setLoading(true);
    setErrors({});
    try {
      await axiosClient.put(`/anios/${id}`, formValues);
      showSuccess('Año académico actualizado exitosamente', 'Éxito');
      navigate('/anios');
    } catch (err: any) {
      if (err.response?.data?.errors) {
        setErrors(err.response.data.errors);
      } else {
        showError(err.response?.data?.message || 'Error al actualizar el año académico', 'Error');
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

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Editar Año Académico</h1>
        <p className="text-gray-600 mt-1">Modifica la información del año académico</p>
      </div>
      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold text-gray-900">Información del Año Académico</h2>
        </CardHeader>
        <CardBody>
          <AnioForm
            values={values}
            onChange={setValues}
            onSubmit={handleSubmit}
            loading={loading}
            errors={errors}
            submitText="Actualizar Año"
          />
        </CardBody>
      </Card>
    </div>
  );
};

export default EditAcademicYearPage; 