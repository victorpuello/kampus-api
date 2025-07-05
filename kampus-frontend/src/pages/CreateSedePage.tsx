import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { SedeForm } from '../components/sedes/SedeForm';
import { Alert } from '../components/ui/Alert';
import PageHeader from '../components/ui/PageHeader';
import axiosClient from '../api/axiosClient';

interface Institucion {
  id: number;
  nombre: string;
  siglas: string;
}

interface SedeFormData {
  institucion_id: number;
  nombre: string;
  direccion: string;
  telefono: string;
}

const CreateSedePage: React.FC = () => {
  const navigate = useNavigate();
  const [instituciones, setInstituciones] = useState<Institucion[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    fetchInstituciones();
  }, []);

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
      
      await axiosClient.post('/sedes', data);
      
      navigate('/sedes', { 
        state: { message: 'Sede creada exitosamente' }
      });
    } catch (err: any) {
      console.error('Error creating sede:', err);
      setError(err.response?.data?.message || 'Error al crear la sede');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="space-y-6">
      <PageHeader
        title="Crear Nueva Sede"
        description="Registra una nueva sede para una institución educativa"
      />

      {error && (
        <Alert
          variant="error"
          message={error}
          onClose={() => setError(null)}
        />
      )}

      <SedeForm
        instituciones={instituciones}
        onSubmit={handleSubmit}
        isLoading={loading}
        submitLabel="Crear Sede"
      />
    </div>
  );
};

export default CreateSedePage; 