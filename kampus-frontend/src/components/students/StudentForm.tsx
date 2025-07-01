import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axiosClient from '../../api/axiosClient';

interface Institution {
  id: number;
  nombre: string;
}

interface Guardian {
  id: number;
  nombre: string;
  apellido: string;
}

interface StudentFormProps {
  studentId?: number;
}

const StudentForm = ({ studentId }: StudentFormProps) => {
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [institutions, setInstitutions] = useState<Institution[]>([]);
  const [guardians, setGuardians] = useState<Guardian[]>([]);
  const [formData, setFormData] = useState({
    nombre: '',
    apellido: '',
    tipo_documento: 'CC',
    numero_documento: '',
    fecha_nacimiento: '',
    genero: 'M',
    direccion: '',
    telefono: '',
    email: '',
    estado: 'activo',
    institucion_id: '',
    acudiente_id: '',
    password: ''
  });

  useEffect(() => {
    const fetchData = async () => {
      try {
        const [institutionsRes, guardiansRes] = await Promise.all([
          axiosClient.get('/instituciones'),
          axiosClient.get('/acudientes')
        ]);
        setInstitutions(institutionsRes.data.data);
        setGuardians(guardiansRes.data.data);
      } catch (err: any) {
        setError(err.response?.data?.message || 'Error al cargar los datos');
      }
    };

    fetchData();

    if (studentId) {
      const fetchStudent = async () => {
        try {
          const response = await axiosClient.get(`/estudiantes/${studentId}`);
          const student = response.data.data;
          setFormData({
            nombre: student.user?.nombre || '',
            apellido: student.user?.apellido || '',
            tipo_documento: student.user?.tipo_documento || 'CC',
            numero_documento: student.user?.numero_documento || '',
            fecha_nacimiento: student.fecha_nacimiento || '',
            genero: student.genero || 'M',
            direccion: student.direccion || '',
            telefono: student.telefono || '',
            email: student.user?.email || '',
            estado: student.estado || 'activo',
            institucion_id: student.institucion_id?.toString() || '',
            acudiente_id: student.acudiente?.id?.toString() || '',
            password: ''
          });
        } catch (err: any) {
          setError(err.response?.data?.message || 'Error al cargar el estudiante');
        }
      };

      fetchStudent();
    }
  }, [studentId]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError(null);

    try {
      // Preparar los datos para enviar
      const submitData = {
        ...formData,
        username: formData.email.split('@')[0], // Generar username automáticamente
        codigo_estudiantil: formData.numero_documento, // Usar número de documento como código
        password: formData.password || 'password123', // Contraseña por defecto si no se proporciona
      };

      if (studentId) {
        await axiosClient.put(`/estudiantes/${studentId}`, submitData);
        alert('Estudiante actualizado exitosamente');
      } else {
        await axiosClient.post('/estudiantes', submitData);
        alert('Estudiante creado exitosamente');
      }
      navigate('/estudiantes');
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || 'Error al guardar el estudiante';
      setError(errorMessage);
      alert(`Error: ${errorMessage}`);
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

  return (
    <form onSubmit={handleSubmit} className="space-y-6">
      {error && (
        <div className="rounded-md bg-red-50 p-4">
          <div className="flex">
            <div className="ml-3">
              <h3 className="text-sm font-medium text-red-800">{error}</h3>
            </div>
          </div>
        </div>
      )}

      <div className="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div>
          <label htmlFor="nombre" className="block text-sm font-medium text-gray-700">
            Nombre
          </label>
          <input
            type="text"
            name="nombre"
            id="nombre"
            required
            value={formData.nombre}
            onChange={handleChange}
            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          />
        </div>

        <div>
          <label htmlFor="apellido" className="block text-sm font-medium text-gray-700">
            Apellido
          </label>
          <input
            type="text"
            name="apellido"
            id="apellido"
            required
            value={formData.apellido}
            onChange={handleChange}
            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          />
        </div>

        <div>
          <label htmlFor="tipo_documento" className="block text-sm font-medium text-gray-700">
            Tipo de Documento
          </label>
          <select
            name="tipo_documento"
            id="tipo_documento"
            required
            value={formData.tipo_documento}
            onChange={handleChange}
            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          >
            <option value="CC">Cédula de Ciudadanía</option>
            <option value="TI">Tarjeta de Identidad</option>
            <option value="CE">Cédula de Extranjería</option>
          </select>
        </div>

        <div>
          <label htmlFor="numero_documento" className="block text-sm font-medium text-gray-700">
            Número de Documento
          </label>
          <input
            type="text"
            name="numero_documento"
            id="numero_documento"
            required
            value={formData.numero_documento}
            onChange={handleChange}
            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          />
        </div>

        <div>
          <label htmlFor="fecha_nacimiento" className="block text-sm font-medium text-gray-700">
            Fecha de Nacimiento
          </label>
          <input
            type="date"
            name="fecha_nacimiento"
            id="fecha_nacimiento"
            required
            value={formData.fecha_nacimiento}
            onChange={handleChange}
            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          />
        </div>

        <div>
          <label htmlFor="genero" className="block text-sm font-medium text-gray-700">
            Género
          </label>
          <select
            name="genero"
            id="genero"
            required
            value={formData.genero}
            onChange={handleChange}
            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          >
            <option value="M">Masculino</option>
            <option value="F">Femenino</option>
            <option value="O">Otro</option>
          </select>
        </div>

        <div>
          <label htmlFor="direccion" className="block text-sm font-medium text-gray-700">
            Dirección
          </label>
          <input
            type="text"
            name="direccion"
            id="direccion"
            required
            value={formData.direccion}
            onChange={handleChange}
            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          />
        </div>

        <div>
          <label htmlFor="telefono" className="block text-sm font-medium text-gray-700">
            Teléfono
          </label>
          <input
            type="tel"
            name="telefono"
            id="telefono"
            required
            value={formData.telefono}
            onChange={handleChange}
            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          />
        </div>

        <div>
          <label htmlFor="email" className="block text-sm font-medium text-gray-700">
            Email
          </label>
          <input
            type="email"
            name="email"
            id="email"
            required
            value={formData.email}
            onChange={handleChange}
            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          />
        </div>

        <div>
          <label htmlFor="estado" className="block text-sm font-medium text-gray-700">
            Estado
          </label>
          <select
            name="estado"
            id="estado"
            required
            value={formData.estado}
            onChange={handleChange}
            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          >
            <option value="activo">Activo</option>
            <option value="inactivo">Inactivo</option>
          </select>
        </div>

        <div>
          <label htmlFor="institucion_id" className="block text-sm font-medium text-gray-700">
            Institución
          </label>
          <select
            name="institucion_id"
            id="institucion_id"
            required
            value={formData.institucion_id}
            onChange={handleChange}
            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          >
            <option value="">Seleccione una institución</option>
            {institutions.map(institution => (
              <option key={institution.id} value={institution.id}>
                {institution.nombre}
              </option>
            ))}
          </select>
        </div>

        <div>
          <label htmlFor="acudiente_id" className="block text-sm font-medium text-gray-700">
            Acudiente
          </label>
          <select
            name="acudiente_id"
            id="acudiente_id"
            value={formData.acudiente_id}
            onChange={handleChange}
            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          >
            <option value="">Seleccione un acudiente</option>
            {guardians.map(guardian => (
              <option key={guardian.id} value={guardian.id}>
                {guardian.nombre} {guardian.apellido}
              </option>
            ))}
          </select>
        </div>
      </div>

      <div className="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
        <button
          type="button"
          onClick={() => navigate('/estudiantes')}
          className="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 w-full sm:w-auto"
        >
          Cancelar
        </button>
        <button
          type="submit"
          disabled={loading}
          className="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 w-full sm:w-auto"
        >
          {loading ? 'Guardando...' : studentId ? 'Actualizar' : 'Crear'}
        </button>
      </div>
    </form>
  );
};

export default StudentForm; 