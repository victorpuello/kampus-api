import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axiosClient from '../api/axiosClient';
import { useAlertContext } from '../contexts/AlertContext';
import { useAuthStore } from '../store/authStore';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
import { GrupoForm } from '../components/grupos/GrupoForm';
import type { GrupoFormValues } from '../components/grupos/GrupoForm';

const CreateGroupPage = () => {
  const navigate = useNavigate();
  const { showSuccess, showError } = useAlertContext();
  const { user } = useAuthStore();
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState<Partial<Record<keyof GrupoFormValues, string>>>({});

  const initialValues: GrupoFormValues = {
    nombre: '',
    sede_id: 0,
    grado_id: 0,
    anio_id: 0,
    director_docente_id: undefined,
    descripcion: '',
    capacidad_maxima: undefined,
    estado: 'activo',
  };

  const [values, setValues] = useState<GrupoFormValues>(initialValues);

  const handleSubmit = async (formValues: GrupoFormValues) => {
    setLoading(true);
    setErrors({});

    try {
      const response = await axiosClient.post('/grupos', formValues);
      showSuccess('Grupo creado exitosamente', 'Éxito');
      navigate('/grupos');
    } catch (err: any) {
      if (err.response?.data?.errors) {
        setErrors(err.response.data.errors);
      } else {
        showError(err.response?.data?.message || 'Error al crear el grupo', 'Error');
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Crear Grupo</h1>
        <p className="text-gray-600 mt-1">
          Completa la información para crear un nuevo grupo académico
        </p>
      </div>

      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold text-gray-900">Información del Grupo</h2>
        </CardHeader>
        <CardBody>
          <GrupoForm
            values={values}
            onChange={setValues}
            onSubmit={handleSubmit}
            loading={loading}
            errors={errors}
            submitText="Crear Grupo"
            institucionId={user?.institucion?.id}
          />
        </CardBody>
      </Card>
    </div>
  );
};

export default CreateGroupPage; 