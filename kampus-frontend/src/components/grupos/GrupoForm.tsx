import React, { useEffect, useState } from 'react';
import { Input } from '../ui/Input';
import { Button } from '../ui/Button';
import axiosClient from '../../api/axiosClient';

export interface GrupoFormValues {
  nombre: string;
  descripcion?: string;
  grado_id: number;
  capacidad_maxima?: number;
  estado: 'activo' | 'inactivo';
}

interface Grado {
  id: number;
  nombre: string;
  nivel?: string;
}

interface GrupoFormProps {
  values: GrupoFormValues;
  onChange: (values: GrupoFormValues) => void;
  onSubmit: (values: GrupoFormValues) => void;
  loading?: boolean;
  errors?: Partial<Record<keyof GrupoFormValues, string>>;
  submitText?: string;
}

export const GrupoForm: React.FC<GrupoFormProps> = ({
  values,
  onChange,
  onSubmit,
  loading = false,
  errors = {},
  submitText = 'Guardar',
}) => {
  const [grados, setGrados] = useState<Grado[]>([]);
  const [loadingGrados, setLoadingGrados] = useState(true);

  useEffect(() => {
    const fetchGrados = async () => {
      try {
        const response = await axiosClient.get('/grados');
        setGrados(response.data.data || response.data);
      } catch (error) {
        console.error('Error al cargar grados:', error);
      } finally {
        setLoadingGrados(false);
      }
    };

    fetchGrados();
  }, []);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    onSubmit(values);
  };

  const handleInputChange = (field: keyof GrupoFormValues, value: string | number) => {
    onChange({
      ...values,
      [field]: value,
    });
  };

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
          disabled={loading || loadingGrados}
        >
          {submitText}
        </Button>
      </div>
    </form>
  );
}; 