import React, { useState, useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { useAlertContext } from '../contexts/AlertContext';
import axiosClient from '../api/axiosClient';
import { 
  PageHeader, 
  Card, 
  CardHeader, 
  CardBody,
  FormContainer,
  FormField,
  FormActions,
  LoadingSpinner
} from '../components/ui';

interface SedeFormData {
  nombre: string;
  direccion: string;
  telefono: string;
}

interface Sede {
  id: number;
  nombre: string;
  direccion: string;
  telefono: string;
  institucion_id: number;
}

const InstitutionSedeEditPage: React.FC = () => {
  const navigate = useNavigate();
  const { id, sedeId } = useParams<{ id: string; sedeId: string }>();
  const { showSuccess, showError } = useAlertContext();
  
  const [formData, setFormData] = useState<SedeFormData>({
    nombre: '',
    direccion: '',
    telefono: ''
  });
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);

  // Validar IDs antes de hacer fetch
  const isValidId = (id: string | undefined): boolean => {
    if (!id) return false;
    const numId = parseInt(id);
    return !isNaN(numId) && numId > 0;
  };

  useEffect(() => {
    const fetchSede = async () => {
      if (!isValidId(sedeId) || !isValidId(id)) {
        setError('ID de sede o institución inválido');
        setLoading(false);
        return;
      }

      try {
        setLoading(true);
        setError(null);
        
        const response = await axiosClient.get(`/sedes/${sedeId}`);
        const sedeData: Sede = response.data.data || response.data;
        
        if (!sedeData || !sedeData.id) {
          setError('La sede no fue encontrada');
          return;
        }
        
        setFormData({
          nombre: sedeData.nombre,
          direccion: sedeData.direccion,
          telefono: sedeData.telefono || ''
        });
        
      } catch (error: any) {
        console.error('Error fetching sede:', error);
        
        if (error.response?.status === 404) {
          setError('La sede no fue encontrada');
        } else if (error.response?.status === 401) {
          setError('No tienes permisos para editar esta sede');
        } else {
          setError('Error al cargar la sede. Inténtalo de nuevo.');
          showError('Error al cargar la sede');
        }
      } finally {
        setLoading(false);
      }
    };

    fetchSede();
  }, [sedeId, id, showError]);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!isValidId(sedeId) || !isValidId(id)) {
      showError('ID de sede o institución inválido');
      return;
    }

    try {
      setSaving(true);
      setError(null);
      
      await axiosClient.put(`/sedes/${sedeId}`, {
        ...formData,
        institucion_id: parseInt(id)
      });
      
      showSuccess('Sede actualizada exitosamente');
      navigate(`/instituciones/${id}/sedes`);
      
    } catch (error: any) {
      console.error('Error updating sede:', error);
      
      if (error.response?.status === 422) {
        const validationErrors = error.response.data.errors;
        const errorMessage = Object.values(validationErrors).flat().join(', ');
        setError(errorMessage);
        showError(errorMessage);
      } else {
        const errorMessage = error.response?.data?.message || 'Error al actualizar la sede';
        setError(errorMessage);
        showError(errorMessage);
      }
    } finally {
      setSaving(false);
    }
  };

  const handleCancel = () => {
    navigate(`/instituciones/${id}/sedes`);
  };

  // Estado de carga
  if (loading) {
    return (
      <div className="space-y-6">
        <PageHeader
          title="Editar Sede"
          description="Modifica la información de la sede"
        />
        <div className="flex items-center justify-center py-12">
          <LoadingSpinner text="Cargando sede..." />
        </div>
      </div>
    );
  }

  // Estado de error
  if (error && !formData.nombre) {
    return (
      <div className="space-y-6">
        <PageHeader
          title="Editar Sede"
          description="Modifica la información de la sede"
        />
        <Card>
          <CardBody>
            <div className="text-center py-8">
              <svg className="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
              </svg>
              <h3 className="mt-2 text-sm font-medium text-gray-900">Error al cargar la sede</h3>
              <p className="mt-1 text-sm text-gray-500">{error}</p>
              <div className="mt-6 flex justify-center space-x-3">
                <Button
                  variant="primary"
                  onClick={() => navigate(`/instituciones/${id}/sedes`)}
                >
                  Volver a las sedes
                </Button>
                <Button
                  variant="secondary"
                  onClick={() => window.location.reload()}
                >
                  Reintentar
                </Button>
              </div>
            </div>
          </CardBody>
        </Card>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <PageHeader
        title="Editar Sede"
        description="Modifica la información de la sede"
      />

      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold text-gray-900">
            Información de la Sede
          </h2>
        </CardHeader>
        <CardBody>
          <FormContainer onSubmit={handleSubmit} error={error}>
            <div className="grid grid-cols-1 gap-6 sm:grid-cols-2">
              <FormField
                label="Nombre"
                name="nombre"
                type="text"
                required
                value={formData.nombre}
                onChange={handleChange}
                placeholder="Ingrese el nombre de la sede"
              />

              <FormField
                label="Teléfono"
                name="telefono"
                type="tel"
                value={formData.telefono}
                onChange={handleChange}
                placeholder="Ingrese el teléfono"
              />

              <div className="sm:col-span-2">
                <FormField
                  label="Dirección"
                  name="direccion"
                  type="text"
                  required
                  value={formData.direccion}
                  onChange={handleChange}
                  placeholder="Ingrese la dirección completa"
                />
              </div>
            </div>

            <FormActions
              onCancel={handleCancel}
              onSubmit={() => {}}
              loading={saving}
              submitText="Actualizar Sede"
              cancelText="Cancelar"
              className="col-span-full"
            />
          </FormContainer>
        </CardBody>
      </Card>
    </div>
  );
};

export default InstitutionSedeEditPage; 