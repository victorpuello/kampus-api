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
  FileUpload
} from '../components/ui';

interface InstitutionFormData {
  nombre: string;
  siglas: string;
  slogan: string;
  dane: string;
  resolucion_aprobacion: string;
  direccion: string;
  telefono: string;
  email: string;
  rector: string;
  escudo: File | null;
  currentEscudoUrl?: string;
}

interface Institution {
  id: number;
  nombre: string;
  siglas: string;
  slogan?: string;
  dane?: string;
  resolucion_aprobacion?: string;
  direccion?: string;
  telefono?: string;
  email?: string;
  rector?: string;
  escudo?: string;
  created_at: string;
  updated_at: string;
}

const EditInstitutionPage: React.FC = () => {
  const navigate = useNavigate();
  const { id } = useParams<{ id: string }>();
  const { showSuccess, showError } = useAlertContext();
  
  const [loading, setLoading] = useState(false);
  const [fetching, setFetching] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [institution, setInstitution] = useState<Institution | null>(null);
  const [formData, setFormData] = useState<InstitutionFormData>({
    nombre: '',
    siglas: '',
    slogan: '',
    dane: '',
    resolucion_aprobacion: '',
    direccion: '',
    telefono: '',
    email: '',
    rector: '',
    escudo: null,
    currentEscudoUrl: ''
  });

  // Validar ID antes de hacer fetch
  const isValidId = (id: string | undefined): boolean => {
    if (!id) return false;
    const numId = parseInt(id);
    return !isNaN(numId) && numId > 0;
  };

  useEffect(() => {
    const fetchInstitution = async () => {
      if (!isValidId(id)) {
        setError('ID de institución inválido');
        setFetching(false);
        return;
      }

      try {
        setFetching(true);
        setError(null);
        
        const response = await axiosClient.get(`/instituciones/${id}`);
        const institutionData = response.data.data || response.data;
        
        if (!institutionData || !institutionData.id) {
          setError('La institución no fue encontrada');
          return;
        }
        
        setInstitution(institutionData);
        
        // Poblar el formulario con los datos existentes
        setFormData({
          nombre: institutionData.nombre || '',
          siglas: institutionData.siglas || '',
          slogan: institutionData.slogan || '',
          dane: institutionData.dane || '',
          resolucion_aprobacion: institutionData.resolucion_aprobacion || '',
          direccion: institutionData.direccion || '',
          telefono: institutionData.telefono || '',
          email: institutionData.email || '',
          rector: institutionData.rector || '',
          escudo: null,
          currentEscudoUrl: institutionData.escudo || ''
        });
        
      } catch (error: any) {
        console.error('Error fetching institution:', error);
        
        if (error.response?.status === 404) {
          setError('La institución no fue encontrada');
        } else if (error.response?.status === 401) {
          setError('No tienes permisos para editar esta institución');
        } else {
          setError('Error al cargar la institución. Inténtalo de nuevo.');
          showError('Error al cargar la institución');
        }
      } finally {
        setFetching(false);
      }
    };

    fetchInstitution();
  }, [id, showError]);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError(null);

    try {
      const formDataToSend = new FormData();
      
      // Agregar todos los campos de texto (incluyendo los vacíos)
      Object.keys(formData).forEach(key => {
        if (key !== 'escudo' && key !== 'currentEscudoUrl') {
          const value = formData[key as keyof InstitutionFormData];
          // Enviar el valor incluso si está vacío para permitir limpiar campos
          formDataToSend.append(key, value !== null && value !== undefined ? value : '');
        }
      });
      
      // Agregar el archivo si existe
      if (formData.escudo) {
        formDataToSend.append('escudo', formData.escudo);
      }

      // Agregar el campo _method para que Laravel procese correctamente el update
      formDataToSend.append('_method', 'PUT');

      console.log('🔄 Enviando datos:', Object.fromEntries(formDataToSend.entries()));

      await axiosClient.post(`/instituciones/${id}`, formDataToSend, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });
      
      showSuccess('Institución actualizada exitosamente');
      navigate(`/instituciones/${id}`);
    } catch (error: any) {
      const errorMessage = error.response?.data?.message || 'Error al actualizar la institución';
      setError(errorMessage);
      showError(errorMessage);
      console.error('❌ Error al actualizar:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleCancel = () => {
    navigate(`/instituciones/${id}`);
  };

  // Estado de carga inicial
  if (fetching) {
    return (
      <div className="space-y-6">
        <PageHeader
          title="Editar Institución"
          description="Modifica la información de la institución"
        />
        <div className="flex items-center justify-center py-12">
          <LoadingSpinner text="Cargando institución..." />
        </div>
      </div>
    );
  }

  // Estado de error
  if (error || !institution) {
    return (
      <div className="space-y-6">
        <PageHeader
          title="Editar Institución"
          description="Modifica la información de la institución"
        />
        <Card>
          <CardBody>
            <div className="text-center py-8">
              <svg className="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
              </svg>
              <h3 className="mt-2 text-sm font-medium text-gray-900">Error al cargar la institución</h3>
              <p className="mt-1 text-sm text-gray-500">{error || 'La institución no fue encontrada'}</p>
              <div className="mt-6">
                <button
                  onClick={() => navigate('/instituciones')}
                  className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Volver a la lista
                </button>
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
        title="Editar Institución"
        description="Modifica la información de la institución educativa"
      />

      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold text-gray-900">
            Información de la Institución
          </h2>
        </CardHeader>
        <CardBody>
          <FormContainer onSubmit={handleSubmit} error={error}>
            <div className="grid grid-cols-1 gap-6 sm:grid-cols-2">
              <FormField
                label="Nombre de la Institución"
                name="nombre"
                type="text"
                required
                value={formData.nombre}
                onChange={handleChange}
                placeholder="Ej: Institución Educativa Ejemplo"
              />

              <FormField
                label="Siglas"
                name="siglas"
                type="text"
                required
                value={formData.siglas}
                onChange={handleChange}
                placeholder="Ej: IEE"
              />

              <FormField
                label="Slogan"
                name="slogan"
                type="text"
                value={formData.slogan}
                onChange={handleChange}
                placeholder="Ej: Educando para el futuro"
              />

              <FormField
                label="Rector"
                name="rector"
                type="text"
                value={formData.rector}
                onChange={handleChange}
                placeholder="Nombre del rector"
              />

              <FormField
                label="Código DANE"
                name="dane"
                type="text"
                value={formData.dane}
                onChange={handleChange}
                placeholder="Ej: 123456789"
              />

              <FormField
                label="Resolución de Aprobación"
                name="resolucion_aprobacion"
                type="text"
                value={formData.resolucion_aprobacion}
                onChange={handleChange}
                placeholder="Ej: Resolución 1234 de 2020"
              />

              <FormField
                label="Dirección"
                name="direccion"
                type="text"
                value={formData.direccion}
                onChange={handleChange}
                placeholder="Dirección completa"
              />

              <FormField
                label="Teléfono"
                name="telefono"
                type="tel"
                value={formData.telefono}
                onChange={handleChange}
                placeholder="Ej: (57) 300-123-4567"
              />

              <FormField
                label="Email"
                name="email"
                type="email"
                value={formData.email}
                onChange={handleChange}
                placeholder="Ej: contacto@institucion.edu.co"
              />

              <FileUpload
                label="Escudo de la Institución"
                name="escudo"
                accept="image/*"
                maxSize={5}
                onFileSelect={(files) => setFormData(prev => ({ ...prev, escudo: files[0] }))}
                onFileRemove={() => setFormData(prev => ({ ...prev, escudo: null }))}
                selectedFiles={formData.escudo ? [formData.escudo] : []}
                error={error || undefined}
              />
            </div>

            <FormActions
              onCancel={handleCancel}
              onSubmit={() => {}}
              loading={loading}
              submitText="Actualizar Institución"
              cancelText="Cancelar"
              className="col-span-full"
            />
          </FormContainer>
        </CardBody>
      </Card>
    </div>
  );
};

export default EditInstitutionPage; 