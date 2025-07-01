import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axiosClient from '../api/axiosClient';
import { useAlertContext } from '../contexts/AlertContext';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
import GradoForm from '../components/grados/GradoForm';
import type { GradoFormValues } from '../components/grados/GradoForm';

const CreateGradePage = () => {
  const navigate = useNavigate();
  const { showSuccess, showError } = useAlertContext();
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState<Partial<Record<keyof GradoFormValues, string>>>({});

  const initialValues: GradoFormValues = {
    nombre: '',
    descripcion: '',
    nivel: '',
    estado: 'activo',
  };

  const [values, setValues] = useState<GradoFormValues>(initialValues);

  const handleSubmit = async (formValues: GradoFormValues) => {
    setLoading(true);
    setErrors({});

    try {
      const response = await axiosClient.post('/grados', formValues);
      showSuccess('Grado creado exitosamente', 'Éxito');
      navigate('/grados');
    } catch (err: any) {
      if (err.response?.data?.errors) {
        setErrors(err.response.data.errors);
      } else {
        showError(err.response?.data?.message || 'Error al crear el grado', 'Error');
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Crear Grado</h1>
        <p className="text-gray-600 mt-1">
          Completa la información para crear un nuevo grado académico
        </p>
      </div>

      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold text-gray-900">Información del Grado</h2>
        </CardHeader>
        <CardBody>
          <GradoForm
            values={values}
            onChange={setValues}
            onSubmit={handleSubmit}
            loading={loading}
            errors={errors}
            submitText="Crear Grado"
          />
        </CardBody>
      </Card>
    </div>
  );
};

export default CreateGradePage; 