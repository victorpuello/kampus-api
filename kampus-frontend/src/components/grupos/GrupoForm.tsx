import React, { useEffect, useState } from 'react';
import { Input } from '../ui/Input';
import { Button } from '../ui/Button';
import axiosClient from '../../api/axiosClient';

export interface GrupoFormValues {
  nombre: string;
  sede_id: number;
  grado_id: number;
  anio_id: number;
  director_docente_id?: number;
  descripcion?: string;
  capacidad_maxima?: number;
  estado: 'activo' | 'inactivo';
}

interface Grado {
  id: number;
  nombre: string;
  nivel?: string;
}

interface Sede {
  id: number;
  nombre: string;
  institucion: {
    id: number;
    nombre: string;
  };
}

interface Anio {
  id: number;
  nombre: string;
  estado: string;
}

interface Docente {
  id: number;
  nombre: string;
  apellido: string;
  email?: string;
  user?: {
    id: number;
    nombre: string;
    apellido: string;
    email: string;
  };
}

interface GrupoFormProps {
  values: GrupoFormValues;
  onChange: (values: GrupoFormValues) => void;
  onSubmit: (values: GrupoFormValues) => void;
  loading?: boolean;
  errors?: Partial<Record<keyof GrupoFormValues, string>>;
  submitText?: string;
  institucionId?: number; // Para filtrar sedes y grados por institución
}

export const GrupoForm: React.FC<GrupoFormProps> = ({
  values,
  onChange,
  onSubmit,
  loading = false,
  errors = {},
  submitText = 'Guardar',
  institucionId,
}) => {
  const [grados, setGrados] = useState<Grado[]>([]);
  const [sedes, setSedes] = useState<Sede[]>([]);
  const [anios, setAnios] = useState<Anio[]>([]);
  const [docentes, setDocentes] = useState<Docente[]>([]);
  const [loadingGrados, setLoadingGrados] = useState(true);
  const [loadingSedes, setLoadingSedes] = useState(true);
  const [loadingAnios, setLoadingAnios] = useState(true);
  const [loadingDocentes, setLoadingDocentes] = useState(true);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const [gradosRes, sedesRes, aniosRes, docentesRes] = await Promise.all([
          axiosClient.get(institucionId ? `/grados?institucion_id=${institucionId}&per_page=100` : '/grados?per_page=100'),
          axiosClient.get(institucionId ? `/sedes?institucion_id=${institucionId}` : '/sedes'),
          axiosClient.get('/anios'),
          axiosClient.get(`/docentes?disponibles_grupo=true${institucionId ? `&institucion_id=${institucionId}` : ''}&per_page=100`)
        ]);
        
        setGrados(gradosRes.data.data || gradosRes.data);
        setSedes(sedesRes.data.data || sedesRes.data);
        setAnios(aniosRes.data.data || aniosRes.data);
        setDocentes(docentesRes.data.data || docentesRes.data);
        
        // Debug: Log de docentes cargados
        console.log('Docentes cargados:', docentesRes.data.data || docentesRes.data);
        
        // Verificar estructura de datos
        const docentesData = docentesRes.data.data || docentesRes.data;
        if (docentesData && docentesData.length > 0) {
            console.log('Primer docente:', docentesData[0]);
        }
      } catch (error) {
        console.error('Error al cargar datos:', error);
      } finally {
        setLoadingGrados(false);
        setLoadingSedes(false);
        setLoadingAnios(false);
        setLoadingDocentes(false);
      }
    };

    fetchData();
  }, [institucionId]);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    
    // Validación adicional en el frontend
    if (!values.sede_id) {
      alert('Por favor selecciona una sede');
      return;
    }
    
    if (!values.grado_id) {
      alert('Por favor selecciona un grado');
      return;
    }
    
    if (!values.anio_id) {
      alert('Por favor selecciona un año académico');
      return;
    }
    
    console.log('Enviando datos del grupo:', values);
    onSubmit(values);
  };

  const handleInputChange = (field: keyof GrupoFormValues, value: string | number) => {
    onChange({
      ...values,
      [field]: value,
    });
  };

  const isLoading = loadingGrados || loadingSedes || loadingAnios || loadingDocentes;

  return (
    <form onSubmit={handleSubmit} className="space-y-6">
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {/* Nombre */}
        <div className="md:col-span-2">
          <label htmlFor="nombre" className="block text-sm font-medium text-gray-700 mb-2">
            Nombre del Grupo *
          </label>
          <Input
            id="nombre"
            type="text"
            value={values.nombre}
            onChange={(e) => handleInputChange('nombre', e.target.value)}
            placeholder="Ej: 10A, 11B, etc."
            error={errors.nombre}
            required
          />
        </div>

        {/* Sede */}
        <div>
          <label htmlFor="sede_id" className="block text-sm font-medium text-gray-700 mb-2">
            Sede *
          </label>
          <select
            id="sede_id"
            value={values.sede_id || ''}
            onChange={(e) => handleInputChange('sede_id', Number(e.target.value))}
            className={`w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 ${
              errors.sede_id ? 'border-red-500' : 'border-gray-300'
            } ${loadingSedes ? 'bg-gray-100' : 'bg-white'}`}
            disabled={loadingSedes}
            required
          >
            <option value="">Seleccionar sede</option>
            {sedes.map((sede) => (
              <option key={sede.id} value={sede.id}>
                {sede.nombre} - {sede.institucion.nombre}
              </option>
            ))}
          </select>
          {errors.sede_id && (
            <p className="mt-1 text-sm text-red-600">{errors.sede_id}</p>
          )}
        </div>

        {/* Grado */}
        <div>
          <label htmlFor="grado_id" className="block text-sm font-medium text-gray-700 mb-2">
            Grado *
          </label>
          <select
            id="grado_id"
            value={values.grado_id || ''}
            onChange={(e) => handleInputChange('grado_id', Number(e.target.value))}
            className={`w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 ${
              errors.grado_id ? 'border-red-500' : 'border-gray-300'
            } ${loadingGrados ? 'bg-gray-100' : 'bg-white'}`}
            disabled={loadingGrados}
            required
          >
            <option value="">Seleccionar grado</option>
            {grados.map((grado) => (
              <option key={grado.id} value={grado.id}>
                {grado.nombre} {grado.nivel && `(${grado.nivel})`}
              </option>
            ))}
          </select>
          {errors.grado_id && (
            <p className="mt-1 text-sm text-red-600">{errors.grado_id}</p>
          )}
        </div>

        {/* Año Académico */}
        <div>
          <label htmlFor="anio_id" className="block text-sm font-medium text-gray-700 mb-2">
            Año Académico *
          </label>
          <select
            id="anio_id"
            value={values.anio_id || ''}
            onChange={(e) => handleInputChange('anio_id', Number(e.target.value))}
            className={`w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 ${
              errors.anio_id ? 'border-red-500' : 'border-gray-300'
            } ${loadingAnios ? 'bg-gray-100' : 'bg-white'}`}
            disabled={loadingAnios}
            required
          >
            <option value="">Seleccionar año</option>
            {anios.map((anio) => (
              <option key={anio.id} value={anio.id}>
                {anio.nombre} ({anio.estado})
              </option>
            ))}
          </select>
          {errors.anio_id && (
            <p className="mt-1 text-sm text-red-600">{errors.anio_id}</p>
          )}
        </div>

        {/* Director de Grupo */}
        <div>
          <label htmlFor="director_docente_id" className="block text-sm font-medium text-gray-700 mb-2">
            Director de Grupo
          </label>
          <select
            id="director_docente_id"
            value={values.director_docente_id || ''}
            onChange={(e) => handleInputChange('director_docente_id', e.target.value ? Number(e.target.value) : 0)}
            className={`w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 ${
              errors.director_docente_id ? 'border-red-500' : 'border-gray-300'
            } ${loadingDocentes ? 'bg-gray-100' : 'bg-white'}`}
            disabled={loadingDocentes}
          >
            <option value="">Sin asignar</option>
            {docentes.map((docente) => {
              // Manejar tanto datos directos como anidados
              const nombre = docente.nombre || docente.user?.nombre || '';
              const apellido = docente.apellido || docente.user?.apellido || '';
              const displayName = `${nombre} ${apellido}`.trim();
              
              return (
                <option key={docente.id} value={docente.id}>
                  {displayName || `Docente ${docente.id}`}
                </option>
              );
            })}
          </select>
          <p className="mt-1 text-sm text-gray-500">
            Solo se muestran docentes que no son directores de otros grupos
          </p>
          {errors.director_docente_id && (
            <p className="mt-1 text-sm text-red-600">{errors.director_docente_id}</p>
          )}
        </div>

        {/* Capacidad Máxima */}
        <div>
          <label htmlFor="capacidad_maxima" className="block text-sm font-medium text-gray-700 mb-2">
            Capacidad Máxima
          </label>
          <Input
            id="capacidad_maxima"
            type="number"
            min="1"
            max="50"
            value={values.capacidad_maxima || ''}
            onChange={(e) => handleInputChange('capacidad_maxima', Number(e.target.value))}
            placeholder="Ej: 30"
            error={errors.capacidad_maxima}
          />
        </div>

        {/* Estado */}
        <div>
          <label htmlFor="estado" className="block text-sm font-medium text-gray-700 mb-2">
            Estado
          </label>
          <select
            id="estado"
            value={values.estado}
            onChange={(e) => handleInputChange('estado', e.target.value as 'activo' | 'inactivo')}
            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white"
          >
            <option value="activo">Activo</option>
            <option value="inactivo">Inactivo</option>
          </select>
          {errors.estado && (
            <p className="mt-1 text-sm text-red-600">{errors.estado}</p>
          )}
        </div>
      </div>

      {/* Descripción */}
      <div>
        <label htmlFor="descripcion" className="block text-sm font-medium text-gray-700 mb-2">
          Descripción
        </label>
        <textarea
          id="descripcion"
          value={values.descripcion || ''}
          onChange={(e) => handleInputChange('descripcion', e.target.value)}
          placeholder="Descripción opcional del grupo..."
          rows={3}
          className={`w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 ${
            errors.descripcion ? 'border-red-500' : 'border-gray-300'
          }`}
        />
        {errors.descripcion && (
          <p className="mt-1 text-sm text-red-600">{errors.descripcion}</p>
        )}
      </div>

      {/* Botones */}
      <div className="flex justify-end space-x-3 pt-6 border-t border-gray-200">
        <Button
          type="button"
          variant="secondary"
          onClick={() => window.history.back()}
          disabled={loading}
        >
          Cancelar
        </Button>
        <Button
          type="submit"
          variant="primary"
          loading={loading}
          disabled={loading || isLoading}
        >
          {submitText}
        </Button>
      </div>
    </form>
  );
}; 