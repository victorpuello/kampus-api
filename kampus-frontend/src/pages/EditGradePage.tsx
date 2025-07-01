import { useState, useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import axiosClient from '../api/axiosClient';
import { useAlertContext } from '../contexts/AlertContext';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
import GradoForm from '../components/grados/GradoForm';
import type { GradoFormValues } from '../components/grados/GradoForm';

const EditGradePage = () => {
  const navigate = useNavigate();
  const { id } = useParams<{ id: string }>();
  const { showSuccess, showError } = useAlertContext();
  const [loading, setLoading] = useState(false);
  const [loadingData, setLoadingData] = useState(true);
  const [errors, setErrors] = useState<Partial<Record<keyof GradoFormValues, string>>>({});

  const initialValues: GradoFormValues = {
    nombre: '',
    descripcion: '',
    nivel: '',
    estado: 'activo',
  };

  const [values, setValues] = useState<GradoFormValues>(initialValues);

  useEffect(() => {
    const fetchGrado = async () => {
      try {
        setLoadingData(true);
        const response = await axiosClient.get(`/grados/${id}`);
        const grado = response.data.data || response.data;
        
        setValues({
          nombre: grado.nombre,
          descripcion: grado.descripcion || '',
          nivel: grado.nivel || '',
          estado: grado.estado,
        });
      } catch (err: any) {
        showError(err.response?.data?.message || 'Error al cargar el grado', 'Error');
        navigate('/grados');
      } finally {
        setLoadingData(false);
      }
    };

    if (id) {
      fetchGrado();
    }
  }, [id, navigate, showError]);

  const handleSubmit = async (formValues: GradoFormValues) => {
    setLoading(true);
    setErrors({});

    try {
      const response = await axiosClient.put(`/grados/${id}`, formValues);
      showSuccess('Grado actualizado exitosamente', 'Éxito');
      navigate('/grados');
    } catch (err: any) {
      if (err.response?.data?.errors) {
        setErrors(err.response.data.errors);
      } else {
        showError(err.response?.data?.message || 'Error al actualizar el grado', 'Error');
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
        <h1 className="text-2xl font-bold text-gray-900">Editar Grado</h1>
        <p className="text-gray-600 mt-1">
          Modifica la información del grado académico
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
            submitText="Actualizar Grado"
          />
        </CardBody>
      </Card>
    </div>
  );
};

export default EditGradePage; 