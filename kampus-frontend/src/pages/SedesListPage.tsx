import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { 
  Button, 
  Input, 
  Card, 
  CardHeader, 
  CardBody, 
  DataTable, 
  Alert, 
  PageHeader 
} from '../components/ui';
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

const SedesListPage: React.FC = () => {
  const [sedes, setSedes] = useState<Sede[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [searchTerm, setSearchTerm] = useState('');
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [itemsPerPage] = useState(10);

  const fetchSedes = async (page = 1, search = '') => {
    try {
      setLoading(true);
      const params = new URLSearchParams({
        page: page.toString(),
        per_page: itemsPerPage.toString(),
      });

      if (search) {
        params.append('search', search);
      }

      const response = await axiosClient.get(`/sedes?${params}`);
      setSedes(response.data.data);
      setTotalPages(Math.ceil(response.data.total / itemsPerPage));
      setCurrentPage(page);
      setError(null);
    } catch (err) {
      console.error('Error fetching sedes:', err);
      setError('Error al cargar las sedes');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchSedes();
  }, []);

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    fetchSedes(1, searchTerm);
  };

  const handleDelete = async (id: number) => {
    if (!confirm('¿Está seguro de que desea eliminar esta sede?')) {
      return;
    }

    try {
      await axiosClient.delete(`/sedes/${id}`);
      fetchSedes(currentPage, searchTerm);
    } catch (err) {
      console.error('Error deleting sede:', err);
      setError('Error al eliminar la sede');
    }
  };

  const columns = [
    {
      key: 'nombre',
      header: 'Nombre',
      accessor: (sede: Sede) => (
        <div>
          <div className="font-medium text-gray-900">{sede.nombre}</div>
          <div className="text-sm text-gray-500">
            {sede.institucion.nombre} ({sede.institucion.siglas})
          </div>
        </div>
      ),
      sortable: true,
    },
    {
      key: 'direccion',
      header: 'Dirección',
      accessor: (sede: Sede) => (
        <div className="max-w-xs truncate" title={sede.direccion}>
          {sede.direccion}
        </div>
      ),
      sortable: true,
    },
    {
      key: 'telefono',
      header: 'Teléfono',
      accessor: (sede: Sede) => sede.telefono || '-',
      sortable: true,
    },
    {
      key: 'actions',
      header: 'Acciones',
      accessor: (sede: Sede) => (
        <div className="flex space-x-2">
          <Link
            to={`/sedes/${sede.id}`}
            className="inline-flex items-center px-2 py-1 text-sm text-blue-600 hover:text-blue-800"
          >
            Ver
          </Link>
          <Link
            to={`/sedes/${sede.id}/editar`}
            className="inline-flex items-center px-2 py-1 text-sm text-green-600 hover:text-green-800"
          >
            Editar
          </Link>
          <button
            onClick={() => handleDelete(sede.id)}
            className="inline-flex items-center px-2 py-1 text-sm text-red-600 hover:text-red-800"
          >
            Eliminar
          </button>
        </div>
      ),
      sortable: false,
    },
  ];

  return (
    <div className="space-y-6">
      <PageHeader
        title="Sedes"
        description="Gestiona las sedes de las instituciones educativas"
      >
        <Link to="/sedes/crear">
          <Button>
            Nueva Sede
          </Button>
        </Link>
      </PageHeader>

      <Card>
        <CardHeader>
          <form onSubmit={handleSearch} className="flex space-x-4">
            <div className="flex-1">
              <Input
                type="text"
                placeholder="Buscar sedes por nombre..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
              />
            </div>
            <Button type="submit">
              Buscar
            </Button>
          </form>
        </CardHeader>
        <CardBody>
          {error && (
            <Alert
              variant="error"
              message={error}
              onClose={() => setError(null)}
            />
          )}

          <DataTable
            data={sedes}
            columns={columns}
            loading={loading}
            error={error}
            searchable={false}
            pagination={false}
            emptyMessage="No se encontraron sedes"
            searchKeys={['nombre', 'direccion', 'institucion.nombre']}
          />
        </CardBody>
      </Card>
    </div>
  );
};

export default SedesListPage; 