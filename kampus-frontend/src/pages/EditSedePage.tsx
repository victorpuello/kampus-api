import React, { useState, useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { SedeForm } from '../components/sedes/SedeForm';
import { Alert } from '../components/ui/Alert';
import PageHeader from '../components/ui/PageHeader';
import axiosClient from '../api/axiosClient';

interface Institucion {
  id: number;
  nombre: string;
  siglas: string;
}

interface Sede {
  id: number;
  institucion_id: number;
  nombre: string;
  direccion: string;
  telefono: string;
}

interface SedeFormData {
  institucion_id: number;
  nombre: string;
  direccion: string;
  telefono: string;
}

const EditSedePage: React.FC = () => {
  const navigate = useNavigate();
  const { id } = useParams<{ id: string }>();
  const [sede, setSede] = useState<Sede | null>(null);
  const [instituciones, setInstituciones] = useState<Institucion[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (id) {
      fetchSede();
      fetchInstituciones();
    }
  }, [id]);

  const fetchSede = async () => {
    try {
      const response = await axiosClient.get(`/sedes/${id}`);
      setSede(response.data);
    } catch (err) {
      console.error('Error fetching sede:', err);
      setError('Error al cargar la sede');
    }
  };

  const fetchInstituciones = async () => {
    try {
      const response = await axiosClient.get('/instituciones?per_page=100');
      setInstituciones(response.data.data);
    } catch (err) {
      console.error('Error fetching instituciones:', err);
      setError('Error al cargar las instituciones');
    }
  };

  const handleSubmit = async (data: SedeFormData) => {
    try {
      setLoading(true);
      setError(null);
      
      await axiosClient.put(`/sedes/${id}`, data);
      
      navigate('/sedes', { 
        state: { message: 'Sede actualizada exitosamente' }
      });
    } catch (err: any) {
      console.error('Error updating sede:', err);
      setError(err.response?.data?.message || 'Error al actualizar la sede');
    } finally {
      setLoading(false);
    }
  };

  if (!sede) {
    return (
      <div className="space-y-6">
        <PageHeader
          title="Editar Sede"
          description="Modifica la información de la sede"
        />
        {error && (
          <Alert
            variant="error"
            message={error}
            onClose={() => setError(null)}
          />
        )}
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <PageHeader
        title="Editar Sede"
        description="Modifica la información de la sede"
      />

      {error && (
        <Alert
          variant="error"
          message={error}
          onClose={() => setError(null)}
        />
      )}

      <SedeForm
        initialData={sede}
        instituciones={instituciones}
        onSubmit={handleSubmit}
        isLoading={loading}
        submitLabel="Actualizar Sede"
      />
    </div>
  );
};

export default EditSedePage; 