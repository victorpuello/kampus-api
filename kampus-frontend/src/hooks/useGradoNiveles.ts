import { useState, useEffect } from 'react';
import axiosClient from '../api/axiosClient';

interface NivelOption {
  value: string;
  label: string;
}

export const useGradoNiveles = () => {
  const [niveles, setNiveles] = useState<NivelOption[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchNiveles = async () => {
      try {
        setLoading(true);
        setError(null);
        
        const response = await axiosClient.get('/grados/niveles');
        const nivelesData = response.data.data || [];
        
        const opciones: NivelOption[] = nivelesData.map((nivel: string) => ({
          value: nivel,
          label: nivel,
        }));
        
        setNiveles(opciones);
      } catch (err: any) {
        console.error('Error al cargar niveles de grado:', err);
        setError(err.response?.data?.message || 'Error al cargar los niveles disponibles');
        
        // Fallback con valores por defecto si la API falla
        const nivelesPorDefecto: NivelOption[] = [
          { value: 'Preescolar', label: 'Preescolar' },
          { value: 'Básica Primaria', label: 'Básica Primaria' },
          { value: 'Básica Secundaria', label: 'Básica Secundaria' },
          { value: 'Educación Media', label: 'Educación Media' },
        ];
        setNiveles(nivelesPorDefecto);
      } finally {
        setLoading(false);
      }
    };

    fetchNiveles();
  }, []);

  return { niveles, loading, error };
}; 