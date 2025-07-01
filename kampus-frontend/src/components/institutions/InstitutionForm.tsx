import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axiosClient from '../../api/axiosClient';
import { useAlertContext } from '../../contexts/AlertContext';
import { 
  FormContainer, 
  FormField, 
  FormSelect, 
  FormActions 
} from '../ui';

interface InstitutionFormProps {
  institutionId?: number;
}

const InstitutionForm = ({ institutionId }: InstitutionFormProps) => {
  const navigate = useNavigate();
  const { showSuccess, showError } = useAlertContext();
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [formData, setFormData] = useState({
    nombre: '',
    siglas: '',
    direccion: '',
    telefono: '',
    email: '',
    sitio_web: '',
    tipo: 'pública',
    estado: 'activo',
    fecha_fundacion: '',
    rector: '',
    secretario_academico: ''
  });

  useEffect(() => {
    if (institutionId) {
      const fetchInstitution = async () => {
        try {
          const response = await axiosClient.get(`/instituciones/${institutionId}`);
          const institution = response.data.data;
          setFormData({
            nombre: institution.nombre || '',
            siglas: institution.siglas || '',
            direccion: institution.direccion || '',
            telefono: institution.telefono || '',
            email: institution.email || '',
            sitio_web: institution.sitio_web || '',
            tipo: institution.tipo || 'pública',
            estado: institution.estado || 'activo',
            fecha_fundacion: institution.fecha_fundacion || '',
            rector: institution.rector || '',
            secretario_academico: institution.secretario_academico || ''
          });
        } catch (err: any) {
          setError(err.response?.data?.message || 'Error al cargar la institución');
        }
      };

      fetchInstitution();
    }
  }, [institutionId]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError(null);

    try {
      if (institutionId) {
        await axiosClient.put(`/instituciones/${institutionId}`, formData);
        showSuccess('Institución actualizada exitosamente', 'Éxito');
      } else {
        await axiosClient.post('/instituciones', formData);
        showSuccess('Institución creada exitosamente', 'Éxito');
      }
      navigate('/instituciones');
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || 'Error al guardar la institución';
      setError(errorMessage);
      showError(errorMessage, 'Error');
    } finally {
      setLoading(false);
    }
  };

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
  };

  const handleCancel = () => {
    navigate('/instituciones');
  };

  const typeOptions = [
    { value: 'pública', label: 'Pública' },
    { value: 'privada', label: 'Privada' },
    { value: 'mixta', label: 'Mixta' }
  ];

  const statusOptions = [
    { value: 'activo', label: 'Activo' },
    { value: 'inactivo', label: 'Inactivo' }
  ];

  return (
    <FormContainer onSubmit={handleSubmit} error={error}>
      <FormField
        label="Nombre de la Institución"
        name="nombre"
        type="text"
        required
        value={formData.nombre}
        onChange={handleChange}
        placeholder="Ingrese el nombre completo de la institución"
      />

      <FormField
        label="Siglas"
        name="siglas"
        type="text"
        required
        value={formData.siglas}
        onChange={handleChange}
        placeholder="Ej: IESJ"
      />

      <FormField
        label="Dirección"
        name="direccion"
        type="text"
        required
        value={formData.direccion}
        onChange={handleChange}
        placeholder="Ingrese la dirección completa"
      />

      <FormField
        label="Teléfono"
        name="telefono"
        type="tel"
        required
        value={formData.telefono}
        onChange={handleChange}
        placeholder="Ingrese el teléfono"
      />

      <FormField
        label="Email"
        name="email"
        type="email"
        required
        value={formData.email}
        onChange={handleChange}
        placeholder="institucion@example.com"
      />

      <FormField
        label="Sitio Web"
        name="sitio_web"
        type="url"
        value={formData.sitio_web}
        onChange={handleChange}
        placeholder="https://www.institucion.edu.co"
      />

      <FormSelect
        label="Tipo de Institución"
        name="tipo"
        required
        value={formData.tipo}
        onChange={handleChange}
        options={typeOptions}
      />

      <FormSelect
        label="Estado"
        name="estado"
        required
        value={formData.estado}
        onChange={handleChange}
        options={statusOptions}
      />

      <FormField
        label="Fecha de Fundación"
        name="fecha_fundacion"
        type="date"
        value={formData.fecha_fundacion}
        onChange={handleChange}
      />

      <FormField
        label="Rector"
        name="rector"
        type="text"
        value={formData.rector}
        onChange={handleChange}
        placeholder="Ingrese el nombre del rector"
      />

      <FormField
        label="Secretario Académico"
        name="secretario_academico"
        type="text"
        value={formData.secretario_academico}
        onChange={handleChange}
        placeholder="Ingrese el nombre del secretario académico"
      />

      <FormActions
        onCancel={handleCancel}
        onSubmit={() => {}}
        loading={loading}
        submitText={institutionId ? 'Actualizar' : 'Crear'}
        cancelText="Cancelar"
        className="col-span-full"
      />
    </FormContainer>
  );
};

export default InstitutionForm; 