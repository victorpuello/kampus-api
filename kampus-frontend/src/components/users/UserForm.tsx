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

interface Role {
  id: number;
  nombre: string;
}

interface UserFormProps {
  userId?: number;
}

const UserForm = ({ userId }: UserFormProps) => {
  const navigate = useNavigate();
  const { showSuccess, showError } = useAlertContext();
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [institutions, setInstitutions] = useState<Institution[]>([]);
  const [roles, setRoles] = useState<Role[]>([]);
  const [formData, setFormData] = useState({
    nombre: '',
    apellido: '',
    email: '',
    username: '',
    password: '',
    tipo_documento: 'CC',
    numero_documento: '',
    estado: 'activo',
    institucion_id: '',
    roles: [] as number[]
  });

  useEffect(() => {
    const fetchData = async () => {
      try {
        const [institutionsRes, rolesRes] = await Promise.all([
          axiosClient.get('/instituciones'),
          axiosClient.get('/roles')
        ]);
        setInstitutions(institutionsRes.data.data);
        setRoles(rolesRes.data.data);
      } catch (err: any) {
        setError(err.response?.data?.message || 'Error al cargar los datos');
      }
    };

    fetchData();

    if (userId) {
      const fetchUser = async () => {
        try {
          const response = await axiosClient.get(`/users/${userId}`);
          const user = response.data.data;
          setFormData({
            nombre: user.nombre || '',
            apellido: user.apellido || '',
            email: user.email || '',
            username: user.username || '',
            password: '', // No cargar contraseña por seguridad
            tipo_documento: user.tipo_documento || 'CC',
            numero_documento: user.numero_documento || '',
            estado: user.estado || 'activo',
            institucion_id: user.institucion_id?.toString() || '',
            roles: user.roles?.map((role: any) => role.id) || []
          });
        } catch (err: any) {
          setError(err.response?.data?.message || 'Error al cargar el usuario');
        }
      };

      fetchUser();
    }
  }, [userId]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError(null);

    try {
      const submitData = {
        ...formData,
        password: formData.password || undefined // Solo enviar si no está vacío
      };

      if (userId) {
        await axiosClient.put(`/users/${userId}`, submitData);
        showSuccess('Usuario actualizado exitosamente', 'Éxito');
      } else {
        await axiosClient.post('/users', submitData);
        showSuccess('Usuario creado exitosamente', 'Éxito');
      }
      navigate('/usuarios');
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || 'Error al guardar el usuario';
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

  const handleRoleChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
    const selectedOptions = Array.from(e.target.selectedOptions, option => Number(option.value));
    setFormData(prev => ({
      ...prev,
      roles: selectedOptions
    }));
  };

  const handleCancel = () => {
    navigate('/usuarios');
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

  const roleOptions = roles.map(role => ({
    value: role.id,
    label: role.nombre
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

      <FormField
        label="Email"
        name="email"
        type="email"
        required
        value={formData.email}
        onChange={handleChange}
        placeholder="usuario@institucion.com"
      />

      <FormField
        label="Nombre de Usuario"
        name="username"
        type="text"
        required
        value={formData.username}
        onChange={handleChange}
        placeholder="Ingrese el nombre de usuario"
      />

      <FormField
        label="Contraseña"
        name="password"
        type="password"
        required={!userId} // Solo requerida para nuevos usuarios
        value={formData.password}
        onChange={handleChange}
        placeholder={userId ? "Dejar vacío para mantener la actual" : "Ingrese la contraseña"}
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

      <div className="col-span-full">
        <label htmlFor="roles" className="block text-sm font-medium text-gray-700">
          Roles <span className="text-red-500">*</span>
        </label>
        <select
          name="roles"
          id="roles"
          required
          multiple
          value={formData.roles.map(String)}
          onChange={handleRoleChange}
          className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          size={4}
        >
          {roleOptions.map((role) => (
            <option key={role.value} value={role.value}>
              {role.label}
            </option>
          ))}
        </select>
        <p className="mt-1 text-sm text-gray-500">
          Mantenga presionado Ctrl (Cmd en Mac) para seleccionar múltiples roles
        </p>
      </div>

      <FormActions
        onCancel={handleCancel}
        onSubmit={() => {}}
        loading={loading}
        submitText={userId ? 'Actualizar' : 'Crear'}
        cancelText="Cancelar"
        className="col-span-full"
      />
    </FormContainer>
  );
};

export default UserForm; 