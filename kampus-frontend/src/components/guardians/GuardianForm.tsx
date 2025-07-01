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



interface GuardianFormProps {
  guardianId?: number;
}

const GuardianForm = ({ guardianId }: GuardianFormProps) => {
  const navigate = useNavigate();
  const { showSuccess, showError } = useAlertContext();
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const [formData, setFormData] = useState({
    nombre: '',
    email: '',
    telefono: ''
  });

    useEffect(() => {
    if (guardianId) {
          const fetchGuardian = async () => {
            try {
              const response = await axiosClient.get(`/acudientes/${guardianId}`);
              const guardian = response.data.data;
              setFormData({
                nombre: guardian.nombre || '',
                email: guardian.email || '',
                telefono: guardian.telefono || ''
              });
            } catch (err: any) {
              setError(err.response?.data?.message || 'Error al cargar el acudiente');
            }
          };

          fetchGuardian();
        }
  }, [guardianId]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError(null);

    try {
      if (guardianId) {
        await axiosClient.put(`/acudientes/${guardianId}`, formData);
        showSuccess('Acudiente actualizado exitosamente', 'Éxito');
      } else {
        await axiosClient.post('/acudientes', formData);
        showSuccess('Acudiente creado exitosamente', 'Éxito');
      }
      navigate('/acudientes');
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || 'Error al guardar el acudiente';
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
    navigate('/acudientes');
  };



  return (
    <FormContainer onSubmit={handleSubmit} error={error}>
      <FormField
        label="Nombre"
        name="nombre"
        type="text"
        required
        value={formData.nombre}
        onChange={handleChange}
        placeholder="Ingrese el nombre"
      />

      <FormField
        label="Email"
        name="email"
        type="email"
        required
        value={formData.email}
        onChange={handleChange}
        placeholder="acudiente@example.com"
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



      <FormActions
        onCancel={handleCancel}
        onSubmit={() => {}}
        loading={loading}
        submitText={guardianId ? 'Actualizar' : 'Crear'}
        cancelText="Cancelar"
        className="col-span-full"
      />
    </FormContainer>
  );
};

export default GuardianForm; 