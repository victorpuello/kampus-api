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

interface Institution {
  id: number;
  nombre: string;
}

interface TeacherFormProps {
  teacherId?: number;
}

const TeacherForm = ({ teacherId }: TeacherFormProps) => {
  const navigate = useNavigate();
  const { showSuccess, showError } = useAlertContext();
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [institutions, setInstitutions] = useState<Institution[]>([]);
  const [formData, setFormData] = useState({
    nombre: '',
    apellido: '',
    tipo_documento: 'CC',
    numero_documento: '',
    email: '',
    telefono: '',
    especialidad: '',
    estado: 'activo',
    institucion_id: '',
    fecha_contratacion: '',
    salario: '',
    horario_trabajo: ''
  });

  useEffect(() => {
    const fetchInstitutions = async () => {
      try {
        const response = await axiosClient.get('/instituciones');
        setInstitutions(response.data.data);
      } catch (err: any) {
        setError(err.response?.data?.message || 'Error al cargar las instituciones');
      }
    };

    fetchInstitutions();

    if (teacherId) {
      const fetchTeacher = async () => {
        try {
          const response = await axiosClient.get(`/docentes/${teacherId}`);
          const teacher = response.data.data;
          setFormData({
            nombre: teacher.user?.nombre || '',
            apellido: teacher.user?.apellido || '',
            tipo_documento: teacher.user?.tipo_documento || 'CC',
            numero_documento: teacher.user?.numero_documento || '',
            email: teacher.user?.email || '',
            telefono: teacher.telefono || '',
            especialidad: teacher.especialidad || '',
            estado: teacher.estado || 'activo',
            institucion_id: teacher.institucion_id?.toString() || '',
            fecha_contratacion: teacher.fecha_contratacion || '',
            salario: teacher.salario?.toString() || '',
            horario_trabajo: teacher.horario_trabajo || ''
          });
        } catch (err: any) {
          setError(err.response?.data?.message || 'Error al cargar el docente');
        }
      };

      fetchTeacher();
    }
  }, [teacherId]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError(null);

    try {
      if (teacherId) {
        await axiosClient.put(`/docentes/${teacherId}`, formData);
        showSuccess('Docente actualizado exitosamente', 'Éxito');
      } else {
        await axiosClient.post('/docentes', formData);
        showSuccess('Docente creado exitosamente', 'Éxito');
      }
      navigate('/docentes');
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || 'Error al guardar el docente';
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
    navigate('/docentes');
  };

  const documentTypeOptions = [
    { value: 'CC', label: 'Cédula de Ciudadanía' },
    { value: 'TI', label: 'Tarjeta de Identidad' },
    { value: 'CE', label: 'Cédula de Extranjería' },
    { value: 'PP', label: 'Pasaporte' }
  ];

  const statusOptions = [
    { value: 'activo', label: 'Activo' },
    { value: 'inactivo', label: 'Inactivo' }
  ];

  const institutionOptions = institutions.map(inst => ({
    value: inst.id,
    label: inst.nombre
  }));

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
        label="Apellido"
        name="apellido"
        type="text"
        required
        value={formData.apellido}
        onChange={handleChange}
        placeholder="Ingrese el apellido"
      />

      <FormSelect
        label="Tipo de Documento"
        name="tipo_documento"
        required
        value={formData.tipo_documento}
        onChange={handleChange}
        options={documentTypeOptions}
      />

      <FormField
        label="Número de Documento"
        name="numero_documento"
        type="text"
        required
        value={formData.numero_documento}
        onChange={handleChange}
        placeholder="Ingrese el número de documento"
      />

      <FormField
        label="Email"
        name="email"
        type="email"
        required
        value={formData.email}
        onChange={handleChange}
        placeholder="docente@institucion.com"
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
        label="Especialidad"
        name="especialidad"
        type="text"
        required
        value={formData.especialidad}
        onChange={handleChange}
        placeholder="Ingrese la especialidad"
      />

      <FormSelect
        label="Estado"
        name="estado"
        required
        value={formData.estado}
        onChange={handleChange}
        options={statusOptions}
      />

      <FormSelect
        label="Institución"
        name="institucion_id"
        required
        value={formData.institucion_id}
        onChange={handleChange}
        options={institutionOptions}
        placeholder="Seleccione una institución"
      />

      <FormField
        label="Fecha de Contratación"
        name="fecha_contratacion"
        type="date"
        value={formData.fecha_contratacion}
        onChange={handleChange}
      />

      <FormField
        label="Salario"
        name="salario"
        type="number"
        value={formData.salario}
        onChange={handleChange}
        placeholder="Ingrese el salario"
        step="0.01"
        min="0"
      />

      <FormField
        label="Horario de Trabajo"
        name="horario_trabajo"
        type="text"
        value={formData.horario_trabajo}
        onChange={handleChange}
        placeholder="Ej: Lunes a Viernes 8:00 AM - 4:00 PM"
      />

      <FormActions
        onCancel={handleCancel}
        onSubmit={() => {}}
        loading={loading}
        submitText={teacherId ? 'Actualizar' : 'Crear'}
        cancelText="Cancelar"
        className="col-span-full"
      />
    </FormContainer>
  );
};

export default TeacherForm; 