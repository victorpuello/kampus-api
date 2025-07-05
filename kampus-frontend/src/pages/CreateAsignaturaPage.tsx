import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axiosClient from '../api/axiosClient';
import { useAlertContext } from '../contexts/AlertContext';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
import { AsignaturaForm } from '../components/asignaturas/AsignaturaForm';
import type { AsignaturaFormValues } from '../components/asignaturas/AsignaturaForm';

const CreateAsignaturaPage = () => {
  const navigate = useNavigate();
  const { showSuccess, showError } = useAlertContext();
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState<Partial<Record<keyof AsignaturaFormValues, string>>>({});

  const initialValues: AsignaturaFormValues = {
    nombre: '',
    codigo: '',
    descripcion: '',
    creditos: 3,
    area_id: 0,
    porcentaje_area: 0,
    estado: 'activo',
    grados: [],
  };

  const [values, setValues] = useState<AsignaturaFormValues>(initialValues);

  const handleSubmit = async (formValues: AsignaturaFormValues) => {
    setLoading(true);
    setErrors({});

    try {
      const response = await axiosClient.post('/asignaturas', formValues);
      showSuccess('Asignatura creada exitosamente', 'Éxito');
      navigate('/asignaturas');
    } catch (err: any) {
      if (err.response?.data?.errors) {
        setErrors(err.response.data.errors);
      } else {
        showError(err.response?.data?.message || 'Error al crear la asignatura', 'Error');
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Crear Asignatura</h1>
        <p className="text-gray-600 mt-1">
          Completa la información para crear una nueva asignatura
        </p>
      </div>

      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold text-gray-900">Información de la Asignatura</h2>
        </CardHeader>
        <CardBody>
          <AsignaturaForm
            values={values}
            onChange={setValues}
            onSubmit={handleSubmit}
            loading={loading}
            errors={errors}
            submitText="Crear Asignatura"
          />
        </CardBody>
      </Card>
    </div>
  );
};

export default CreateAsignaturaPage; 