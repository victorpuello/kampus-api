import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axiosClient from '../api/axiosClient';
import { useAlertContext } from '../contexts/AlertContext';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
import { AreaForm } from '../components/areas/AreaForm';
import type { AreaFormValues } from '../components/areas/AreaForm';
import { useAuthStore } from '../store/authStore';

const CreateAreaPage = () => {
  const navigate = useNavigate();
  const { showSuccess, showError } = useAlertContext();
  const { user } = useAuthStore();
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState<Partial<Record<keyof AreaFormValues, string>>>({});

  const initialValues: AreaFormValues = {
    nombre: '',
    descripcion: '',
    codigo: '',
    color: '#3B82F6',
    estado: 'activo',
    institucion_id: user?.institucion?.id || 0,
  };

  const [values, setValues] = useState<AreaFormValues>(initialValues);

  const handleSubmit = async (formValues: AreaFormValues) => {
    setLoading(true);
    setErrors({});

    try {
      const response = await axiosClient.post('/areas', formValues);
      showSuccess('Área creada exitosamente', 'Éxito');
      navigate('/areas');
    } catch (err: any) {
      if (err.response?.data?.errors) {
        setErrors(err.response.data.errors);
      } else {
        showError(err.response?.data?.message || 'Error al crear el área', 'Error');
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Crear Área</h1>
        <p className="text-gray-600 mt-1">
          Completa la información para crear una nueva área académica
        </p>
      </div>

      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold text-gray-900">Información del Área</h2>
        </CardHeader>
        <CardBody>
          <AreaForm
            values={values}
            onChange={setValues}
            onSubmit={handleSubmit}
            loading={loading}
            errors={errors}
            submitText="Crear Área"
          />
        </CardBody>
      </Card>
    </div>
  );
};

export default CreateAreaPage; 