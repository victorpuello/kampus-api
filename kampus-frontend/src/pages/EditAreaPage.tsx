import { useState, useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import axiosClient from '../api/axiosClient';
import { useAlertContext } from '../contexts/AlertContext';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
import { AreaForm } from '../components/areas/AreaForm';
import type { AreaFormValues } from '../components/areas/AreaForm';

const EditAreaPage = () => {
  const navigate = useNavigate();
  const { id } = useParams<{ id: string }>();
  const { showSuccess, showError } = useAlertContext();
  const [loading, setLoading] = useState(false);
  const [loadingData, setLoadingData] = useState(true);
  const [errors, setErrors] = useState<Partial<Record<keyof AreaFormValues, string>>>({});

  const initialValues: AreaFormValues = {
    nombre: '',
    descripcion: '',
    codigo: '',
    color: '#3B82F6',
    estado: 'activo',
  };

  const [values, setValues] = useState<AreaFormValues>(initialValues);

  useEffect(() => {
    const fetchArea = async () => {
      try {
        setLoadingData(true);
        const response = await axiosClient.get(`/areas/${id}`);
        const area = response.data.data || response.data;
        
        setValues({
          nombre: area.nombre,
          descripcion: area.descripcion || '',
          codigo: area.codigo || '',
          color: area.color || '#3B82F6',
          estado: area.estado,
        });
      } catch (err: any) {
        showError(err.response?.data?.message || 'Error al cargar el área', 'Error');
        navigate('/areas');
      } finally {
        setLoadingData(false);
      }
    };

    if (id) {
      fetchArea();
    }
  }, [id, navigate, showError]);

  const handleSubmit = async (formValues: AreaFormValues) => {
    setLoading(true);
    setErrors({});

    try {
      const response = await axiosClient.put(`/areas/${id}`, formValues);
      showSuccess('Área actualizada exitosamente', 'Éxito');
      navigate('/areas');
    } catch (err: any) {
      if (err.response?.data?.errors) {
        setErrors(err.response.data.errors);
      } else {
        showError(err.response?.data?.message || 'Error al actualizar el área', 'Error');
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
        <h1 className="text-2xl font-bold text-gray-900">Editar Área</h1>
        <p className="text-gray-600 mt-1">
          Modifica la información del área académica
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
            submitText="Actualizar Área"
          />
        </CardBody>
      </Card>
    </div>
  );
};

export default EditAreaPage; 