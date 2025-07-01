import { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import axiosClient from '../api/axiosClient';
import { Button } from '../components/ui/Button';
import { Input } from '../components/ui/Input';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
import { useAlertContext } from '../contexts/AlertContext';

interface Institution {
  id: number;
  nombre: string;
}

interface Teacher {
  id: number;
  user: {
    nombre: string;
    apellido: string;
    tipo_documento: string;
    numero_documento: string;
    email: string;
  };
  telefono: string;
  especialidad: string;
  estado: string;
  institucion_id: number;
  fecha_contratacion?: string;
  salario?: number;
  horario_trabajo?: string;
}

const EditTeacherPage = () => {
  const { id } = useParams<{ id: string }>();
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
    const fetchData = async () => {
      try {
        const [teacherRes, institutionsRes] = await Promise.all([
          axiosClient.get(`/docentes/${id}`),
          axiosClient.get('/instituciones')
        ]);
        
        const teacher = teacherRes.data.data;
        setFormData({
          nombre: teacher.user.nombre,
          apellido: teacher.user.apellido,
          tipo_documento: teacher.user.tipo_documento,
          numero_documento: teacher.user.numero_documento,
          email: teacher.user.email,
          telefono: teacher.telefono,
          especialidad: teacher.especialidad,
          estado: teacher.estado,
          institucion_id: teacher.institucion_id.toString(),
          fecha_contratacion: teacher.fecha_contratacion || '',
          salario: teacher.salario?.toString() || '',
          horario_trabajo: teacher.horario_trabajo || ''
        });
        
        setInstitutions(institutionsRes.data.data);
      } catch (err: any) {
        setError(err.response?.data?.message || 'Error al cargar los datos');
      }
    };

    fetchData();
  }, [id]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError(null);

    try {
      await axiosClient.put(`/docentes/${id}`, formData);
      showSuccess('Docente actualizado exitosamente', 'Éxito');
      navigate('/docentes');
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || 'Error al actualizar el docente';
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

  return (
    <div className="space-y-6">
      <div className="sm:flex sm:items-center">
        <div className="sm:flex-auto">
          <h1 className="text-xl font-semibold text-gray-900">Editar Docente</h1>
          <p className="mt-2 text-sm text-gray-700">
            Modifique la información del docente según sea necesario.
          </p>
        </div>
      </div>

      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold text-gray-900">Información del Docente</h2>
        </CardHeader>
        <CardBody>
          {error && (
            <div className="mb-6 rounded-md bg-red-50 p-4">
              <div className="flex">
                <div className="ml-3">
                  <h3 className="text-sm font-medium text-red-800">{error}</h3>
                </div>
              </div>
            </div>
          )}

          <form onSubmit={handleSubmit} className="space-y-6">
            <div className="grid grid-cols-1 gap-6 sm:grid-cols-2">
              <Input
                label="Nombre"
                name="nombre"
                type="text"
                required
                value={formData.nombre}
                onChange={handleChange}
                placeholder="Ingrese el nombre"
              />

              <Input
                label="Apellido"
                name="apellido"
                type="text"
                required
                value={formData.apellido}
                onChange={handleChange}
                placeholder="Ingrese el apellido"
              />

              <div>
                <label htmlFor="tipo_documento" className="block text-sm font-medium text-gray-700 mb-1">
                  Tipo de Documento
                </label>
                <select
                  name="tipo_documento"
                  id="tipo_documento"
                  required
                  value={formData.tipo_documento}
                  onChange={handleChange}
                  className="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                >
                  <option value="CC">Cédula de Ciudadanía</option>
                  <option value="TI">Tarjeta de Identidad</option>
                  <option value="CE">Cédula de Extranjería</option>
                  <option value="PP">Pasaporte</option>
                </select>
              </div>

              <Input
                label="Número de Documento"
                name="numero_documento"
                type="text"
                required
                value={formData.numero_documento}
                onChange={handleChange}
                placeholder="Ingrese el número de documento"
              />

              <Input
                label="Email"
                name="email"
                type="email"
                required
                value={formData.email}
                onChange={handleChange}
                placeholder="docente@institucion.com"
              />

              <Input
                label="Teléfono"
                name="telefono"
                type="tel"
                required
                value={formData.telefono}
                onChange={handleChange}
                placeholder="Ingrese el teléfono"
              />

              <Input
                label="Especialidad"
                name="especialidad"
                type="text"
                required
                value={formData.especialidad}
                onChange={handleChange}
                placeholder="Ingrese la especialidad"
              />

              <div>
                <label htmlFor="estado" className="block text-sm font-medium text-gray-700 mb-1">
                  Estado
                </label>
                <select
                  name="estado"
                  id="estado"
                  required
                  value={formData.estado}
                  onChange={handleChange}
                  className="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                >
                  <option value="activo">Activo</option>
                  <option value="inactivo">Inactivo</option>
                </select>
              </div>

              <div>
                <label htmlFor="institucion_id" className="block text-sm font-medium text-gray-700 mb-1">
                  Institución
                </label>
                <select
                  name="institucion_id"
                  id="institucion_id"
                  required
                  value={formData.institucion_id}
                  onChange={handleChange}
                  className="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                >
                  <option value="">Seleccione una institución</option>
                  {institutions.map(institution => (
                    <option key={institution.id} value={institution.id}>
                      {institution.nombre}
                    </option>
                  ))}
                </select>
              </div>

              <Input
                label="Fecha de Contratación"
                name="fecha_contratacion"
                type="date"
                value={formData.fecha_contratacion}
                onChange={handleChange}
              />

              <Input
                label="Salario"
                name="salario"
                type="number"
                value={formData.salario}
                onChange={handleChange}
                placeholder="Ingrese el salario"
              />

              <Input
                label="Horario de Trabajo"
                name="horario_trabajo"
                type="text"
                value={formData.horario_trabajo}
                onChange={handleChange}
                placeholder="Ej: Lunes a Viernes 8:00 AM - 4:00 PM"
              />
            </div>

            <div className="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
              <Button
                type="button"
                variant="secondary"
                onClick={() => navigate('/docentes')}
                className="w-full sm:w-auto"
              >
                Cancelar
              </Button>
              <Button
                type="submit"
                loading={loading}
                className="w-full sm:w-auto"
              >
                {loading ? 'Actualizando...' : 'Actualizar Docente'}
              </Button>
            </div>
          </form>
        </CardBody>
      </Card>
    </div>
  );
};

export default EditTeacherPage; 