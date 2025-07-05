import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
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
}

const CreateInstitutionPage: React.FC = () => {
  const navigate = useNavigate();
  const { showSuccess, showError } = useAlertContext();
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
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
    escudo: null
  });

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
      
      // Agregar todos los campos de texto
      Object.keys(formData).forEach(key => {
        if (key !== 'escudo' && formData[key as keyof InstitutionFormData]) {
          formDataToSend.append(key, formData[key as keyof InstitutionFormData] as string);
        }
      });
      
      // Agregar el archivo si existe
      if (formData.escudo) {
        formDataToSend.append('escudo', formData.escudo);
      }

      await axiosClient.post('/instituciones', formDataToSend, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });
      
      showSuccess('Institución creada exitosamente');
      navigate('/instituciones');
    } catch (error: any) {
      const errorMessage = error.response?.data?.message || 'Error al crear la institución';
      setError(errorMessage);
      showError(errorMessage);
    } finally {
      setLoading(false);
    }
  };

  const handleCancel = () => {
    navigate('/instituciones');
  };

  return (
    <div className="space-y-6">
      <PageHeader
        title="Crear Nueva Institución"
        description="Complete el formulario para registrar una nueva institución educativa"
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
              submitText="Crear Institución"
              cancelText="Cancelar"
              className="col-span-full"
            />
          </FormContainer>
        </CardBody>
      </Card>
    </div>
  );
};

export default CreateInstitutionPage; 