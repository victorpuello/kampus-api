import React, { useEffect, useState } from 'react';
import { Input } from '../ui/Input';
import { Button } from '../ui/Button';
import axiosClient from '../../api/axiosClient';

export interface AsignaturaFormValues {
  nombre: string;
  codigo?: string;
  descripcion?: string;
  creditos?: number;
  area_id: number;
  estado: 'activo' | 'inactivo';
  grados?: number[];
}

interface Area {
  id: number;
  nombre: string;
  codigo?: string;
  color?: string;
}

interface Grado {
  id: number;
  nombre: string;
  nivel?: string;
}

interface AsignaturaFormProps {
  values: AsignaturaFormValues;
  onChange: (values: AsignaturaFormValues) => void;
  onSubmit: (values: AsignaturaFormValues) => void;
  loading?: boolean;
  errors?: Partial<Record<keyof AsignaturaFormValues, string>>;
  submitText?: string;
}

export const AsignaturaForm: React.FC<AsignaturaFormProps> = ({
  values,
  onChange,
  onSubmit,
  loading = false,
  errors = {},
  submitText = 'Guardar',
}) => {
  const [areas, setAreas] = useState<Area[]>([]);
  const [grados, setGrados] = useState<Grado[]>([]);
  const [loadingAreas, setLoadingAreas] = useState(true);
  const [loadingGrados, setLoadingGrados] = useState(true);

  useEffect(() => {
    const fetchData = async () => {
      try {
        // Cargar áreas
        const areasResponse = await axiosClient.get('/areas');
        setAreas(areasResponse.data.data || areasResponse.data);
        
        // Cargar grados
        const gradosResponse = await axiosClient.get('/grados');
        setGrados(gradosResponse.data.data || gradosResponse.data);
      } catch (error) {
        console.error('Error al cargar datos:', error);
      } finally {
        setLoadingAreas(false);
        setLoadingGrados(false);
      }
    };

    fetchData();
  }, []);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    onSubmit(values);
  };

  const handleInputChange = (field: keyof AsignaturaFormValues, value: string | number | number[]) => {
    onChange({
      ...values,
      [field]: value,
    });
  };

  const handleGradoToggle = (gradoId: number) => {
    const currentGrados = values.grados || [];
    const newGrados = currentGrados.includes(gradoId)
      ? currentGrados.filter(id => id !== gradoId)
      : [...currentGrados, gradoId];
    
    handleInputChange('grados', newGrados);
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-6">
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {/* Nombre */}
        <div className="md:col-span-2">
          <label htmlFor="nombre" className="block text-sm font-medium text-gray-700 mb-2">
            Nombre de la Asignatura *
          </label>
          <Input
            id="nombre"
            type="text"
            value={values.nombre}
            onChange={(e) => handleInputChange('nombre', e.target.value)}
            placeholder="Ej: Matemáticas Básicas, Historia Universal..."
            error={errors.nombre}
            required
          />
        </div>

        {/* Código */}
        <div>
          <label htmlFor="codigo" className="block text-sm font-medium text-gray-700 mb-2">
            Código
          </label>
          <Input
            id="codigo"
            type="text"
            value={values.codigo || ''}
            onChange={(e) => handleInputChange('codigo', e.target.value)}
            placeholder="Ej: MAT101, HIS201..."
            error={errors.codigo}
          />
        </div>

        {/* Créditos */}
        <div>
          <label htmlFor="creditos" className="block text-sm font-medium text-gray-700 mb-2">
            Créditos
          </label>
          <Input
            id="creditos"
            type="number"
            min="1"
            max="10"
            value={values.creditos || ''}
            onChange={(e) => handleInputChange('creditos', Number(e.target.value))}
            placeholder="Ej: 3"
            error={errors.creditos}
          />
        </div>

        {/* Área */}
        <div>
          <label htmlFor="area_id" className="block text-sm font-medium text-gray-700 mb-2">
            Área *
          </label>
          <select
            id="area_id"
            value={values.area_id || ''}
            onChange={(e) => handleInputChange('area_id', Number(e.target.value))}
            className={`w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 ${
              errors.area_id ? 'border-red-500' : 'border-gray-300'
            } ${loadingAreas ? 'bg-gray-100' : 'bg-white'}`}
            disabled={loadingAreas}
            required
          >
            <option value="">Seleccionar área</option>
            {areas.map((area) => (
              <option key={area.id} value={area.id}>
                {area.nombre} {area.codigo && `(${area.codigo})`}
              </option>
            ))}
          </select>
          {errors.area_id && (
            <p className="mt-1 text-sm text-red-600">{errors.area_id}</p>
          )}
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

      {/* Grados donde se imparte */}
      <div>
        <label className="block text-sm font-medium text-gray-700 mb-2">
          Grados donde se imparte
        </label>
        {loadingGrados ? (
          <div className="text-sm text-gray-500">Cargando grados...</div>
        ) : (
          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
            {grados.map((grado) => (
              <label key={grado.id} className="flex items-center space-x-2 cursor-pointer">
                <input
                  type="checkbox"
                  checked={(values.grados || []).includes(grado.id)}
                  onChange={() => handleGradoToggle(grado.id)}
                  className="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                />
                <span className="text-sm text-gray-700">
                  {grado.nombre} {grado.nivel && `(${grado.nivel})`}
                </span>
              </label>
            ))}
          </div>
        )}
        {errors.grados && (
          <p className="mt-1 text-sm text-red-600">{errors.grados}</p>
        )}
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
          placeholder="Descripción detallada de la asignatura..."
          rows={4}
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
          disabled={loading || loadingAreas || loadingGrados}
        >
          {submitText}
        </Button>
      </div>
    </form>
  );
}; 