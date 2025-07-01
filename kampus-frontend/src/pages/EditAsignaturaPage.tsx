import { useState, useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import axiosClient from '../api/axiosClient';
import { useAlertContext } from '../contexts/AlertContext';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
import { AsignaturaForm } from '../components/asignaturas/AsignaturaForm';
import type { AsignaturaFormValues } from '../components/asignaturas/AsignaturaForm';

const EditAsignaturaPage = () => {
  const navigate = useNavigate();
  const { id } = useParams<{ id: string }>();
  const { showSuccess, showError } = useAlertContext();
  const [loading, setLoading] = useState(false);
  const [loadingData, setLoadingData] = useState(true);
  const [errors, setErrors] = useState<Partial<Record<keyof AsignaturaFormValues, string>>>({});

  const initialValues: AsignaturaFormValues = {
    nombre: '',
    codigo: '',
    descripcion: '',
    creditos: 3,
    area_id: 0,
    estado: 'activo',
    grados: [],
  };

  const [values, setValues] = useState<AsignaturaFormValues>(initialValues);

  useEffect(() => {
    const fetchAsignatura = async () => {
      try {
        setLoadingData(true);
        const response = await axiosClient.get(`/asignaturas/${id}`);
        const asignatura = response.data.data || response.data;
        
        setValues({
          nombre: asignatura.nombre,
          codigo: asignatura.codigo || '',
          descripcion: asignatura.descripcion || '',
          creditos: asignatura.creditos || 3,
          area_id: asignatura.area_id || asignatura.area?.id || 0,
          estado: asignatura.estado,
          grados: asignatura.grados?.map((g: any) => g.id) || asignatura.grados || [],
        });
      } catch (err: any) {
        showError(err.response?.data?.message || 'Error al cargar la asignatura', 'Error');
        navigate('/asignaturas');
      } finally {
        setLoadingData(false);
      }
    };

    if (id) {
      fetchAsignatura();
    }
  }, [id, navigate, showError]);

  const handleSubmit = async (formValues: AsignaturaFormValues) => {
    setLoading(true);
    setErrors({});

    try {
      const response = await axiosClient.put(`/asignaturas/${id}`, formValues);
      showSuccess('Asignatura actualizada exitosamente', 'Éxito');
      navigate('/asignaturas');
    } catch (err: any) {
      if (err.response?.data?.errors) {
        setErrors(err.response.data.errors);
      } else {
        showError(err.response?.data?.message || 'Error al actualizar la asignatura', 'Error');
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
        <h1 className="text-2xl font-bold text-gray-900">Editar Asignatura</h1>
        <p className="text-gray-600 mt-1">
          Modifica la información de la asignatura
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
            submitText="Actualizar Asignatura"
          />
        </CardBody>
      </Card>
    </div>
  );
};

export default EditAsignaturaPage; 