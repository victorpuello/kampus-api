import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axiosClient from '../api/axiosClient';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
import { AnioForm } from '../components/anios/AnioForm';
import type { AnioFormValues } from '../components/anios/AnioForm';
import { useAlertContext } from '../contexts/AlertContext';

const CreateAcademicYearPage = () => {
  const navigate = useNavigate();
  const { showSuccess, showError } = useAlertContext();
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState<Partial<Record<keyof AnioFormValues, string>>>({});

  const initialValues: AnioFormValues = {
    nombre: '',
    fecha_inicio: '',
    fecha_fin: '',
    institucion_id: 0,
    estado: 'activo',
  };

  const [values, setValues] = useState<AnioFormValues>(initialValues);

  const handleSubmit = async (formValues: AnioFormValues) => {
    setLoading(true);
    setErrors({});
    try {
      await axiosClient.post('/anios', formValues);
      showSuccess('Año académico creado exitosamente', 'Éxito');
      navigate('/anios');
    } catch (err: any) {
      if (err.response?.data?.errors) {
        setErrors(err.response.data.errors);
      } else {
        showError(err.response?.data?.message || 'Error al crear el año académico', 'Error');
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Crear Año Académico</h1>
        <p className="text-gray-600 mt-1">Completa la información para crear un nuevo año académico</p>
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
            submitText="Crear Año"
          />
        </CardBody>
      </Card>
    </div>
  );
};

export default CreateAcademicYearPage; 