import { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import axiosClient from '../api/axiosClient';
import { Button } from '../components/ui/Button';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
import { Badge } from '../components/ui/Badge';

interface Student {
  id: number;
  user?: {
    nombre: string;
    apellido: string;
    tipo_documento: string;
    numero_documento: string;
    email: string;
  };
  fecha_nacimiento: string;
  genero: string;
  direccion: string;
  telefono: string;
  estado: string;
  institucion?: {
    id: number;
    nombre: string;
  };
  acudiente?: {
    id: number;
    nombre: string;
    apellido: string;
    telefono: string;
    email: string;
  };
  historial_grupos?: Array<{
    id: number;
    grupo: {
      id: number;
      nombre: string;
      grado: string;
    };
    fecha_inicio: string;
    fecha_fin: string | null;
  }>;
  observador?: Array<{
    id: number;
    fecha: string;
    descripcion: string;
    tipo: string;
  }>;
  notas?: Array<{
    id: number;
    asignatura: {
      id: number;
      nombre: string;
    };
    valor: number;
    periodo: string;
    fecha: string;
  }>;
  inasistencias?: Array<{
    id: number;
    fecha: string;
    tipo: string;
    justificada: boolean;
    descripcion: string;
  }>;
}

const StudentDetailPage = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const [student, setStudent] = useState<Student | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchStudent = async () => {
      try {
        const response = await axiosClient.get(`/estudiantes/${id}`);
        console.log('Datos del estudiante recibidos:', response.data);
        setStudent(response.data.data);
        setError(null);
      } catch (err: any) {
        console.error('Error al cargar estudiante:', err);
        setError(err.response?.data?.message || 'Error al cargar el estudiante');
      } finally {
        setLoading(false);
      }
    };

    fetchStudent();
  }, [id]);

  const handleDelete = async () => {
    const studentName = student?.user ? `${student.user.nombre} ${student.user.apellido}` : 'este estudiante';
    if (!window.confirm(`¿Estás seguro de que deseas eliminar a ${studentName}? Esta acción no se puede deshacer.`)) {
      return;
    }

    try {
      setLoading(true);
      await axiosClient.delete(`/estudiantes/${id}`);
      alert('Estudiante eliminado exitosamente');
      navigate('/estudiantes');
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || 'Error al eliminar el estudiante';
      alert(`Error: ${errorMessage}`);
      setError(errorMessage);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="flex justify-center items-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900"></div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="rounded-md bg-red-50 p-4">
        <div className="flex">
          <div className="ml-3">
            <h3 className="text-sm font-medium text-red-800">{error}</h3>
          </div>
        </div>
      </div>
    );
  }

  if (!student) {
    return (
      <div className="text-center">
        <h3 className="text-lg font-medium text-gray-900">Estudiante no encontrado</h3>
      </div>
    );
  }

  // Verificar si tenemos datos básicos del estudiante
  if (!student.user) {
    return (
      <div className="text-center">
        <h3 className="text-lg font-medium text-gray-900">Datos del estudiante incompletos</h3>
        <p className="text-sm text-gray-500 mt-2">La información del usuario no está disponible</p>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div className="sm:flex sm:items-center sm:justify-between flex-wrap">
        <div>
          <h1 className="text-2xl font-semibold text-gray-900">
            {student.user.nombre} {student.user.apellido}
          </h1>
          <p className="mt-2 text-sm text-gray-700">
            {student.user.tipo_documento} {student.user.numero_documento}
          </p>
        </div>
        <div className="mt-4 sm:mt-0 flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
          <Button
            variant="primary"
            onClick={() => navigate(`/estudiantes/${id}/editar`)}
            leftIcon={
              <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
            }
          >
            Editar
          </Button>
          <Button
            variant="danger"
            onClick={handleDelete}
            leftIcon={
              <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            }
          >
            Eliminar
          </Button>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Información Personal */}
        <Card>
          <CardHeader>
            <h3 className="text-lg font-semibold text-gray-900">Información Personal</h3>
          </CardHeader>
          <CardBody>
            <div className="space-y-4">
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Nombre Completo:</span>
                <span className="text-sm text-gray-900">
                  {student.user.nombre} {student.user.apellido}
                </span>
              </div>
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Documento:</span>
                <span className="text-sm text-gray-900">
                  {student.user.tipo_documento} {student.user.numero_documento}
                </span>
              </div>
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Email:</span>
                <span className="text-sm text-gray-900">{student.user.email}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Fecha de Nacimiento:</span>
                <span className="text-sm text-gray-900">
                  {student.fecha_nacimiento ? new Date(student.fecha_nacimiento).toLocaleDateString() : 'No especificada'}
                </span>
              </div>
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Género:</span>
                <span className="text-sm text-gray-900">
                  {student.genero === 'M' ? 'Masculino' : student.genero === 'F' ? 'Femenino' : 'Otro'}
                </span>
              </div>
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Dirección:</span>
                <span className="text-sm text-gray-900">{student.direccion || 'No especificada'}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Teléfono:</span>
                <span className="text-sm text-gray-900">{student.telefono || 'No especificado'}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Estado:</span>
                <Badge
                  variant={student.estado === 'activo' ? 'success' : 'error'}
                >
                  {student.estado}
                </Badge>
              </div>
            </div>
          </CardBody>
        </Card>

        {/* Información Académica */}
        <Card>
          <CardHeader>
            <h3 className="text-lg font-semibold text-gray-900">Información Académica</h3>
          </CardHeader>
          <CardBody>
            <div className="space-y-4">
              <div className="flex justify-between">
                <span className="text-sm font-medium text-gray-500">Institución:</span>
                <span className="text-sm text-gray-900">{student.institucion?.nombre || 'No asignada'}</span>
              </div>
              {student.acudiente && (
                <div className="border-t pt-4">
                  <h4 className="text-sm font-medium text-gray-700 mb-2">Acudiente</h4>
                  <div className="space-y-2">
                    <div className="flex justify-between">
                      <span className="text-sm text-gray-500">Nombre:</span>
                      <span className="text-sm text-gray-900">
                        {student.acudiente.nombre} {student.acudiente.apellido}
                      </span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-sm text-gray-500">Teléfono:</span>
                      <span className="text-sm text-gray-900">{student.acudiente.telefono}</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-sm text-gray-500">Email:</span>
                      <span className="text-sm text-gray-900">{student.acudiente.email}</span>
                    </div>
                  </div>
                </div>
              )}
            </div>
          </CardBody>
        </Card>
      </div>

      {/* Historial de Grupos */}
      {student.historial_grupos && student.historial_grupos.length > 0 && (
        <Card>
          <CardHeader>
            <h3 className="text-lg font-semibold text-gray-900">Historial de Grupos</h3>
          </CardHeader>
          <CardBody>
            <div className="overflow-x-auto">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Grupo
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Grado
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Fecha Inicio
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Fecha Fin
                    </th>
                  </tr>
                </thead>
                <tbody className="bg-white divide-y divide-gray-200">
                  {student.historial_grupos.map((historial) => (
                    <tr key={historial.id}>
                      <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {historial.grupo.nombre}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {historial.grupo.grado}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {new Date(historial.fecha_inicio).toLocaleDateString()}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {historial.fecha_fin
                          ? new Date(historial.fecha_fin).toLocaleDateString()
                          : 'Actual'}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </CardBody>
        </Card>
      )}

      {/* Observaciones */}
      {student.observador && student.observador.length > 0 && (
        <Card>
          <CardHeader>
            <h3 className="text-lg font-semibold text-gray-900">Observaciones</h3>
          </CardHeader>
          <CardBody>
            <div className="overflow-x-auto">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Fecha
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Tipo
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Descripción
                    </th>
                  </tr>
                </thead>
                <tbody className="bg-white divide-y divide-gray-200">
                  {student.observador.map((observacion) => (
                    <tr key={observacion.id}>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {new Date(observacion.fecha).toLocaleDateString()}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {observacion.tipo}
                      </td>
                      <td className="px-6 py-4 text-sm text-gray-500">{observacion.descripcion}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </CardBody>
        </Card>
      )}

      {/* Notas */}
      {student.notas && student.notas.length > 0 && (
        <Card>
          <CardHeader>
            <h3 className="text-lg font-semibold text-gray-900">Notas</h3>
          </CardHeader>
          <CardBody>
            <div className="overflow-x-auto">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Asignatura
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Valor
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Período
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Fecha
                    </th>
                  </tr>
                </thead>
                <tbody className="bg-white divide-y divide-gray-200">
                  {student.notas.map((nota) => (
                    <tr key={nota.id}>
                      <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {nota.asignatura.nombre}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {nota.valor}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {nota.periodo}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {new Date(nota.fecha).toLocaleDateString()}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </CardBody>
        </Card>
      )}

      {/* Inasistencias */}
      {student.inasistencias && student.inasistencias.length > 0 && (
        <Card>
          <CardHeader>
            <h3 className="text-lg font-semibold text-gray-900">Inasistencias</h3>
          </CardHeader>
          <CardBody>
            <div className="overflow-x-auto">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Fecha
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Tipo
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Justificada
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Descripción
                    </th>
                  </tr>
                </thead>
                <tbody className="bg-white divide-y divide-gray-200">
                  {student.inasistencias.map((inasistencia) => (
                    <tr key={inasistencia.id}>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {new Date(inasistencia.fecha).toLocaleDateString()}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {inasistencia.tipo}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <Badge
                          variant={inasistencia.justificada ? 'success' : 'error'}
                        >
                          {inasistencia.justificada ? 'Sí' : 'No'}
                        </Badge>
                      </td>
                      <td className="px-6 py-4 text-sm text-gray-500">{inasistencia.descripcion}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </CardBody>
        </Card>
      )}

      {/* Acciones adicionales */}
      <Card>
        <CardHeader>
          <h3 className="text-lg font-semibold text-gray-900">Acciones</h3>
        </CardHeader>
        <CardBody>
          <div className="flex flex-wrap gap-3">
            <Button
              variant="secondary"
              onClick={() => navigate('/estudiantes')}
              leftIcon={
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
              }
            >
              Volver a la Lista
            </Button>
            <Button
              variant="primary"
              onClick={() => navigate(`/estudiantes/${id}/editar`)}
              leftIcon={
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              }
            >
              Editar Estudiante
            </Button>
            <Button
              variant="danger"
              onClick={handleDelete}
              leftIcon={
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              }
            >
              Eliminar Estudiante
            </Button>
          </div>
        </CardBody>
      </Card>
    </div>
  );
};

export default StudentDetailPage; 