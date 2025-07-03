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
  LoadingSpinner,
  Button
} from '../components/ui';

interface SedeFormData {
  nombre: string;
  direccion: string;
  telefono: string;
}

interface Institucion {
  id: number;
  nombre: string;
  siglas: string;
}

const InstitutionSedeCreatePage: React.FC = () => {
  const navigate = useNavigate();
  const { institutionId } = useParams<{ institutionId: string }>();
  const { showSuccess, showError } = useAlertContext();
  
  const [formData, setFormData] = useState<SedeFormData>({
    nombre: '',
    direccion: '',
    telefono: ''
  });
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [institucion, setInstitucion] = useState<Institucion | null>(null);

  // Validar ID de institución antes de hacer fetch
  const isValidId = (id: string | undefined): boolean => {
    if (!id) return false;
    const numId = parseInt(id);
    return !isNaN(numId) && numId > 0;
  };

  useEffect(() => {
    const fetchInstitucion = async () => {
      if (!isValidId(institutionId)) {
        setError('ID de institución inválido');
        setLoading(false);
        return;
      }

      try {
        setLoading(true);
        setError(null);
        
        const response = await axiosClient.get(`/instituciones/${institutionId}`);
        const institucionData: Institucion = response.data.data || response.data;
        
        if (!institucionData || !institucionData.id) {
          setError('La institución no fue encontrada');
          return;
        }
        
        setInstitucion(institucionData);
        
      } catch (error: any) {
        console.error('Error fetching institución:', error);
        
        if (error.response?.status === 404) {
          setError('La institución no fue encontrada');
        } else if (error.response?.status === 401) {
          setError('No tienes permisos para crear sedes en esta institución');
        } else {
          setError('Error al cargar la institución. Inténtalo de nuevo.');
          showError('Error al cargar la institución');
        }
      } finally {
        setLoading(false);
      }
    };

    fetchInstitucion();
  }, [institutionId, showError]);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!isValidId(institutionId)) {
      showError('ID de institución inválido');
      return;
    }

    try {
      setSaving(true);
      setError(null);
      
      await axiosClient.post('/sedes', {
        ...formData,
        institucion_id: parseInt(institutionId!)
      });
      
      showSuccess('Sede creada exitosamente');
      navigate(`/instituciones/${institutionId}/sedes`);
      
    } catch (error: any) {
      console.error('Error creating sede:', error);
      
      if (error.response?.status === 422) {
        const validationErrors = error.response.data.errors;
        const errorMessage = Object.values(validationErrors).flat().join(', ');
        setError(errorMessage);
        showError(errorMessage);
      } else {
        const errorMessage = error.response?.data?.message || 'Error al crear la sede';
        setError(errorMessage);
        showError(errorMessage);
      }
    } finally {
      setSaving(false);
    }
  };

  const handleCancel = () => {
    navigate(`/instituciones/${institutionId}/sedes`);
  };

  // Estado de carga
  if (loading) {
    return (
      <div className="space-y-6">
        <PageHeader
          title="Crear Nueva Sede"
          description="Agrega una nueva sede a la institución"
        />
        <div className="flex items-center justify-center py-12">
          <LoadingSpinner text="Cargando institución..." />
        </div>
      </div>
    );
  }

  // Estado de error
  if (error && !institucion) {
    return (
      <div className="space-y-6">
        <PageHeader
          title="Crear Nueva Sede"
          description="Agrega una nueva sede a la institución"
        />
        <Card>
          <CardBody>
            <div className="text-center py-8">
              <svg className="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
              </svg>
              <h3 className="mt-2 text-sm font-medium text-gray-900">Error al cargar la institución</h3>
              <p className="mt-1 text-sm text-gray-500">{error}</p>
              <div className="mt-6 flex justify-center space-x-3">
                <Button
                  variant="primary"
                  onClick={() => navigate(`/instituciones/${institutionId}/sedes`)}
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
        title="Crear Nueva Sede"
        description={`Agrega una nueva sede a ${institucion?.nombre}`}
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
              submitText="Crear Sede"
              cancelText="Cancelar"
              className="col-span-full"
            />
          </FormContainer>
        </CardBody>
      </Card>
    </div>
  );
};

export default InstitutionSedeCreatePage; 