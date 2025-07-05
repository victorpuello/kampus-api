import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import { Button } from '../components/ui/Button';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
import { Alert } from '../components/ui/Alert';
import PageHeader from '../components/ui/PageHeader';
import axiosClient from '../api/axiosClient';

interface Sede {
  id: number;
  nombre: string;
  direccion: string;
  telefono: string;
  institucion: {
    id: number;
    nombre: string;
    siglas: string;
  };
  created_at: string;
  updated_at: string;
}

const SedeDetailPage: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const [sede, setSede] = useState<Sede | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (id) {
      fetchSede();
    }
  }, [id]);

  const fetchSede = async () => {
    try {
      setLoading(true);
      const response = await axiosClient.get(`/sedes/${id}`);
      setSede(response.data);
      setError(null);
    } catch (err) {
      console.error('Error fetching sede:', err);
      setError('Error al cargar la sede');
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="space-y-6">
        <PageHeader
          title="Detalle de Sede"
          description="Información detallada de la sede"
        />
        <div className="text-center py-8">
          <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
          <p className="mt-2 text-gray-600">Cargando...</p>
        </div>
      </div>
    );
  }

  if (error || !sede) {
    return (
      <div className="space-y-6">
        <PageHeader
          title="Detalle de Sede"
          description="Información detallada de la sede"
        />
        <Alert
          variant="error"
          message={error || 'Sede no encontrada'}
          onClose={() => setError(null)}
        />
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <PageHeader
        title="Detalle de Sede"
        description="Información detallada de la sede"
      >
        <div className="flex space-x-2">
          <Link to={`/sedes/${sede.id}/edit`}>
            <Button variant="secondary">
              Editar
            </Button>
          </Link>
          <Link to="/sedes">
            <Button variant="secondary">
              Volver
            </Button>
          </Link>
        </div>
      </PageHeader>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Información Principal */}
        <Card>
          <CardHeader>
            <h3 className="text-lg font-semibold text-gray-900">Información General</h3>
          </CardHeader>
          <CardBody>
            <dl className="space-y-4">
              <div>
                <dt className="text-sm font-medium text-gray-500">Nombre de la Sede</dt>
                <dd className="mt-1 text-sm text-gray-900">{sede.nombre}</dd>
              </div>
              
              <div>
                <dt className="text-sm font-medium text-gray-500">Institución</dt>
                <dd className="mt-1 text-sm text-gray-900">
                  {sede.institucion.nombre} ({sede.institucion.siglas})
                </dd>
              </div>

              <div>
                <dt className="text-sm font-medium text-gray-500">Dirección</dt>
                <dd className="mt-1 text-sm text-gray-900">{sede.direccion}</dd>
              </div>

              <div>
                <dt className="text-sm font-medium text-gray-500">Teléfono</dt>
                <dd className="mt-1 text-sm text-gray-900">{sede.telefono || 'No especificado'}</dd>
              </div>
            </dl>
          </CardBody>
        </Card>

        {/* Información de la Institución */}
        <Card>
          <CardHeader>
            <h3 className="text-lg font-semibold text-gray-900">Institución</h3>
          </CardHeader>
          <CardBody>
            <dl className="space-y-4">
              <div>
                <dt className="text-sm font-medium text-gray-500">Nombre</dt>
                <dd className="mt-1 text-sm text-gray-900">{sede.institucion.nombre}</dd>
              </div>
              
              <div>
                <dt className="text-sm font-medium text-gray-500">Siglas</dt>
                <dd className="mt-1 text-sm text-gray-900">{sede.institucion.siglas}</dd>
              </div>

              <div className="pt-4">
                <Link 
                  to={`/instituciones/${sede.institucion.id}`}
                  className="text-blue-600 hover:text-blue-800 text-sm font-medium"
                >
                  Ver detalles de la institución →
                </Link>
              </div>
            </dl>
          </CardBody>
        </Card>
      </div>

      {/* Información de Auditoría */}
      <Card>
        <CardHeader>
          <h3 className="text-lg font-semibold text-gray-900">Información de Auditoría</h3>
        </CardHeader>
        <CardBody>
          <dl className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <dt className="text-sm font-medium text-gray-500">Fecha de Creación</dt>
              <dd className="mt-1 text-sm text-gray-900">
                {new Date(sede.created_at).toLocaleDateString('es-ES', {
                  year: 'numeric',
                  month: 'long',
                  day: 'numeric',
                  hour: '2-digit',
                  minute: '2-digit'
                })}
              </dd>
            </div>
            
            <div>
              <dt className="text-sm font-medium text-gray-500">Última Actualización</dt>
              <dd className="mt-1 text-sm text-gray-900">
                {new Date(sede.updated_at).toLocaleDateString('es-ES', {
                  year: 'numeric',
                  month: 'long',
                  day: 'numeric',
                  hour: '2-digit',
                  minute: '2-digit'
                })}
              </dd>
            </div>
          </dl>
        </CardBody>
      </Card>
    </div>
  );
};

export default SedeDetailPage; 