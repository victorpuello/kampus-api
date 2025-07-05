import { useState, useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import axiosClient from '../api/axiosClient';
import { useAlertContext } from '../contexts/AlertContext';
import { useAuthStore } from '../store/authStore';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
import { GrupoForm } from '../components/grupos/GrupoForm';
import type { GrupoFormValues } from '../components/grupos/GrupoForm';

const EditGroupPage = () => {
  const navigate = useNavigate();
  const { id } = useParams<{ id: string }>();
  const { showSuccess, showError } = useAlertContext();
  const { user } = useAuthStore();
  const [loading, setLoading] = useState(false);
  const [loadingData, setLoadingData] = useState(true);
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

  useEffect(() => {
    const fetchGrupo = async () => {
      try {
        setLoadingData(true);
        const response = await axiosClient.get(`/grupos/${id}`);
        const grupo = response.data.data || response.data;
        
        setValues({
          nombre: grupo.nombre,
          sede_id: grupo.sede_id,
          grado_id: grupo.grado_id,
          anio_id: grupo.anio_id,
          director_docente_id: grupo.director_docente_id,
          descripcion: grupo.descripcion || '',
          capacidad_maxima: grupo.capacidad_maxima,
          estado: grupo.estado,
        });
      } catch (err: any) {
        showError(err.response?.data?.message || 'Error al cargar el grupo', 'Error');
        navigate('/grupos');
      } finally {
        setLoadingData(false);
      }
    };

    if (id) {
      fetchGrupo();
    }
  }, [id, navigate, showError]);

  const handleSubmit = async (formValues: GrupoFormValues) => {
    setLoading(true);
    setErrors({});

    try {
      const response = await axiosClient.put(`/grupos/${id}`, formValues);
      showSuccess('Grupo actualizado exitosamente', 'Éxito');
      navigate('/grupos');
    } catch (err: any) {
      if (err.response?.data?.errors) {
        setErrors(err.response.data.errors);
      } else {
        showError(err.response?.data?.message || 'Error al actualizar el grupo', 'Error');
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
        <h1 className="text-2xl font-bold text-gray-900">Editar Grupo</h1>
        <p className="text-gray-600 mt-1">
          Modifica la información del grupo académico
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
            submitText="Actualizar Grupo"
            institucionId={user?.institucion?.id}
          />
        </CardBody>
      </Card>
    </div>
  );
};

export default EditGroupPage; 