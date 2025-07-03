import React, { useState, useEffect } from 'react';
import { useNavigate, useParams, Link } from 'react-router-dom';
import { useAlertContext } from '../contexts/AlertContext';
import { useConfirm } from '../hooks/useConfirm';
import axiosClient from '../api/axiosClient';
import { PageHeader, Button, Badge, Card, CardHeader, CardBody, LoadingSpinner } from '../components/ui';

interface Sede {
  id: number;
  nombre: string;
  direccion: string;
  telefono: string;
  created_at: string;
  updated_at: string;
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
  sedes?: Sede[];
}

const InstitutionDetailPage: React.FC = () => {
  const navigate = useNavigate();
  const { id } = useParams<{ id: string }>();
  const { showSuccess, showError } = useAlertContext();
  const { confirm } = useConfirm();
  
  const [institution, setInstitution] = useState<Institution | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

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
        setLoading(false);
        return;
      }

      try {
        setLoading(true);
        setError(null);
        
        const response = await axiosClient.get(`/instituciones/${id}?include=sedes`);
        const institutionData = response.data.data || response.data;
        
        if (!institutionData || !institutionData.id) {
          setError('La institución no fue encontrada');
          return;
        }
        
        setInstitution(institutionData);
        
      } catch (error: any) {
        console.error('Error fetching institution:', error);
        
        if (error.response?.status === 404) {
          setError('La institución no fue encontrada');
        } else if (error.response?.status === 401) {
          setError('No tienes permisos para ver esta institución');
        } else {
          setError('Error al cargar la institución. Inténtalo de nuevo.');
          showError('Error al cargar la institución');
        }
      } finally {
        setLoading(false);
      }
    };

    fetchInstitution();
  }, [id, showError]);

  const handleDelete = async () => {
    if (!institution) {
      showError('No se puede eliminar una institución que no existe');
      return;
    }

    const confirmed = await confirm({
      title: '¿Eliminar institución?',
      message: `¿Estás seguro de que quieres eliminar la institución "${institution.nombre}"? Esta acción no se puede deshacer y eliminará todos los datos asociados, incluyendo las sedes.`,
      confirmText: 'Sí, eliminar',
      cancelText: 'Cancelar',
      variant: 'danger'
    });

    if (confirmed) {
      try {
        setLoading(true);
        await axiosClient.delete(`/instituciones/${institution.id}`);
        showSuccess('Institución eliminada exitosamente');
        navigate('/instituciones');
      } catch (error: any) {
        console.error('Error deleting institution:', error);
        const errorMessage = error.response?.data?.message || 'Error al eliminar la institución';
        showError(errorMessage);
      } finally {
        setLoading(false);
      }
    }
  };

  const handleEdit = () => {
    if (!institution) {
      showError('No se puede editar una institución que no existe');
      return;
    }
    navigate(`/instituciones/${institution.id}/editar`);
  };

  const formatDate = (dateString: string) => {
    try {
      return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      });
    } catch (error) {
      return 'Fecha inválida';
    }
  };

  // Estado de carga
  if (loading) {
    return (
      <div className="space-y-6">
        <PageHeader
          title="Detalles de la Institución"
          description="Información completa de la institución educativa"
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
          title="Detalles de la Institución"
          description="Información completa de la institución educativa"
        />
        <Card>
          <CardBody>
            <div className="text-center py-8">
              <svg className="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
              </svg>
              <h3 className="mt-2 text-sm font-medium text-gray-900">Error al cargar la institución</h3>
              <p className="mt-1 text-sm text-gray-500">{error || 'La institución no fue encontrada'}</p>
              <div className="mt-6 flex justify-center space-x-3">
                <Button
                  variant="primary"
                  onClick={() => navigate('/instituciones')}
                >
                  Volver a la lista
                </Button>
                <Button
                  variant="secondary"
                  onClick={() => window.location.reload()}
                >
                  Reintentar
                </Button>
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
        title="Detalles de la Institución"
        description="Información completa de la institución educativa"
      >
        <div className="flex space-x-3">
          <Link to={`/instituciones/${institution?.id}/sedes`}>
            <Button variant="secondary">Ver Sedes</Button>
          </Link>
          <Button variant="primary" onClick={handleEdit} disabled={!institution}>
            Editar
          </Button>
          <Button variant="danger" onClick={handleDelete} disabled={!institution}>
            Eliminar
          </Button>
        </div>
      </PageHeader>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Información General */}
        <Card>
          <CardHeader>
            <h3 className="text-lg font-semibold text-gray-900">Información General</h3>
          </CardHeader>
          <CardBody>
            <dl className="space-y-4">
              <div>
                <dt className="text-sm font-medium text-gray-500">ID de la Institución</dt>
                <dd className="mt-1 text-sm text-gray-900">{institution.id}</dd>
              </div>
              
              <div>
                <dt className="text-sm font-medium text-gray-500">Nombre Completo</dt>
                <dd className="mt-1 text-sm text-gray-900">{institution.nombre}</dd>
              </div>

              <div>
                <dt className="text-sm font-medium text-gray-500">Siglas</dt>
                <dd className="mt-1">
                  <Badge variant="default">{institution.siglas}</Badge>
                </dd>
              </div>

              {institution.slogan && (
                <div>
                  <dt className="text-sm font-medium text-gray-500">Slogan</dt>
                  <dd className="mt-1 text-sm text-gray-900 italic">"{institution.slogan}"</dd>
                </div>
              )}

              {institution.rector && (
                <div>
                  <dt className="text-sm font-medium text-gray-500">Rector</dt>
                  <dd className="mt-1 text-sm text-gray-900">{institution.rector}</dd>
                </div>
              )}
            </dl>
          </CardBody>
        </Card>

        {/* Información Oficial */}
        <Card>
          <CardHeader>
            <h3 className="text-lg font-semibold text-gray-900">Información Oficial</h3>
          </CardHeader>
          <CardBody>
            <dl className="space-y-4">
              {institution.dane && (
                <div>
                  <dt className="text-sm font-medium text-gray-500">Código DANE</dt>
                  <dd className="mt-1 text-sm text-gray-900">{institution.dane}</dd>
                </div>
              )}

              {institution.resolucion_aprobacion && (
                <div>
                  <dt className="text-sm font-medium text-gray-500">Resolución de Aprobación</dt>
                  <dd className="mt-1 text-sm text-gray-900">{institution.resolucion_aprobacion}</dd>
                </div>
              )}

              {institution.escudo && (
                <div>
                  <dt className="text-sm font-medium text-gray-500">Escudo</dt>
                  <dd className="mt-1">
                    <div className="flex items-center space-x-3">
                      <img 
                        src={institution.escudo} 
                        alt="Escudo de la institución"
                        className="w-24 h-24 object-contain rounded-lg border shadow-sm"
                      />
                      <div className="flex-1">
                        <p className="text-xs text-gray-500">Escudo oficial de la institución</p>
                        <a 
                          href={institution.escudo} 
                          target="_blank" 
                          rel="noopener noreferrer"
                          className="text-xs text-blue-600 hover:text-blue-800"
                        >
                          Ver imagen completa
                        </a>
                      </div>
                    </div>
                  </dd>
                </div>
              )}
            </dl>
          </CardBody>
        </Card>
      </div>

      {/* Información de Contacto */}
      <Card>
        <CardHeader>
          <h3 className="text-lg font-semibold text-gray-900">Información de Contacto</h3>
        </CardHeader>
        <CardBody>
          <dl className="grid grid-cols-1 md:grid-cols-2 gap-4">
            {institution.direccion && (
              <div>
                <dt className="text-sm font-medium text-gray-500">Dirección</dt>
                <dd className="mt-1 text-sm text-gray-900">{institution.direccion}</dd>
              </div>
            )}

            {institution.telefono && (
              <div>
                <dt className="text-sm font-medium text-gray-500">Teléfono</dt>
                <dd className="mt-1 text-sm text-gray-900">{institution.telefono}</dd>
              </div>
            )}

            {institution.email && (
              <div>
                <dt className="text-sm font-medium text-gray-500">Email</dt>
                <dd className="mt-1 text-sm text-gray-900">
                  <a href={`mailto:${institution.email}`} className="text-blue-600 hover:text-blue-800">
                    {institution.email}
                  </a>
                </dd>
              </div>
            )}
          </dl>
        </CardBody>
      </Card>

      {/* Sedes de la Institución */}
      <Card>
        <CardHeader>
          <div className="flex justify-between items-center">
            <h3 className="text-lg font-semibold text-gray-900">Sedes de la Institución</h3>
            <Link to="/sedes/crear" state={{ institucion_id: institution.id }}>
              <Button variant="secondary" size="sm">
                <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Nueva Sede
              </Button>
            </Link>
          </div>
        </CardHeader>
        <CardBody>
          {institution.sedes && institution.sedes.length > 0 ? (
            <div className="space-y-4">
              {institution.sedes.map((sede) => (
                <div key={sede.id} className="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                  <div className="flex justify-between items-start">
                    <div className="flex-1">
                      <h4 className="text-sm font-medium text-gray-900">{sede.nombre}</h4>
                      <p className="text-sm text-gray-600 mt-1">{sede.direccion}</p>
                      {sede.telefono && (
                        <p className="text-sm text-gray-500 mt-1">📞 {sede.telefono}</p>
                      )}
                    </div>
                    <div className="flex space-x-2 ml-4">
                      <Link
                        to={`/sedes/${sede.id}`}
                        className="text-blue-600 hover:text-blue-800 text-sm font-medium"
                      >
                        Ver
                      </Link>
                      <Link
                        to={`/sedes/${sede.id}/editar`}
                        className="text-green-600 hover:text-green-800 text-sm font-medium"
                      >
                        Editar
                      </Link>
                    </div>
                  </div>
                </div>
              ))}
              <div className="mt-4 pt-4 border-t border-gray-200">
                <Link
                  to={`/instituciones/${institution.id}/sedes`}
                  className="text-blue-600 hover:text-blue-800 text-sm font-medium"
                >
                  Ver todas las sedes →
                </Link>
              </div>
            </div>
          ) : (
            <div className="text-center py-8">
              <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
              <h3 className="mt-2 text-sm font-medium text-gray-900">No hay sedes registradas</h3>
              <p className="mt-1 text-sm text-gray-500">Esta institución aún no tiene sedes configuradas.</p>
              <div className="mt-6">
                <Link to="/sedes/crear" state={{ institucion_id: institution.id }}>
                  <Button variant="secondary">
                    <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Agregar Primera Sede
                  </Button>
                </Link>
              </div>
            </div>
          )}
        </CardBody>
      </Card>

      {/* Información de Auditoría */}
      <Card>
        <CardHeader>
          <h3 className="text-lg font-semibold text-gray-900">Información de Auditoría</h3>
        </CardHeader>
        <CardBody>
          <dl className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <dt className="text-sm font-medium text-gray-500">Fecha de Creación</dt>
              <dd className="mt-1 text-sm text-gray-900">{formatDate(institution.created_at)}</dd>
            </div>
            
            <div>
              <dt className="text-sm font-medium text-gray-500">Última Actualización</dt>
              <dd className="mt-1 text-sm text-gray-900">{formatDate(institution.updated_at)}</dd>
            </div>
          </dl>
        </CardBody>
      </Card>
    </div>
  );
};

export default InstitutionDetailPage; 