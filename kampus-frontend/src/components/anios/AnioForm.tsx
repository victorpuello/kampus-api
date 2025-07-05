import React, { useEffect, useState } from 'react';
import { Input } from '../ui/Input';
import { Button } from '../ui/Button';
import axiosClient from '../../api/axiosClient';

export interface AnioFormValues {
  nombre: string;
  fecha_inicio: string;
  fecha_fin: string;
  institucion_id: number;
  estado: 'activo' | 'inactivo';
}

interface Institucion {
  id: number;
  nombre: string;
  siglas: string;
}

interface AnioFormProps {
  values: AnioFormValues;
  onChange: (values: AnioFormValues) => void;
  onSubmit: (values: AnioFormValues) => void;
  loading?: boolean;
  errors?: Partial<Record<keyof AnioFormValues, string>>;
  submitText?: string;
}

export const AnioForm: React.FC<AnioFormProps> = ({
  values,
  onChange,
  onSubmit,
  loading = false,
  errors = {},
  submitText = 'Guardar',
}) => {
  const [instituciones, setInstituciones] = useState<Institucion[]>([]);
  const [loadingInstituciones, setLoadingInstituciones] = useState(true);

  useEffect(() => {
    const fetchInstituciones = async () => {
      try {
        const response = await axiosClient.get('/instituciones');
        setInstituciones(response.data.data || response.data);
      } catch (error) {
        console.error('Error al cargar instituciones:', error);
      } finally {
        setLoadingInstituciones(false);
      }
    };

    fetchInstituciones();
  }, []);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    onSubmit(values);
  };

  const handleInputChange = (field: keyof AnioFormValues, value: string | number) => {
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
            Nombre del Año Académico *
          </label>
          <Input
            id="nombre"
            type="text"
            value={values.nombre}
            onChange={(e) => handleInputChange('nombre', e.target.value)}
            placeholder="Ej: 2024-2025"
            error={errors.nombre}
            required
          />
        </div>

        {/* Institución */}
        <div className="md:col-span-2">
          <label htmlFor="institucion_id" className="block text-sm font-medium text-gray-700 mb-2">
            Institución *
          </label>
          <select
            id="institucion_id"
            value={values.institucion_id || ''}
            onChange={(e) => handleInputChange('institucion_id', Number(e.target.value))}
            className={`w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 ${
              errors.institucion_id ? 'border-red-500' : 'border-gray-300'
            } ${loadingInstituciones ? 'bg-gray-100' : 'bg-white'}`}
            disabled={loadingInstituciones}
            required
          >
            <option value="">Seleccionar institución</option>
            {instituciones.map((institucion) => (
              <option key={institucion.id} value={institucion.id}>
                {institucion.nombre} ({institucion.siglas})
              </option>
            ))}
          </select>
          {errors.institucion_id && (
            <p className="mt-1 text-sm text-red-600">{errors.institucion_id}</p>
          )}
        </div>

        {/* Fecha inicio */}
        <div>
          <label htmlFor="fecha_inicio" className="block text-sm font-medium text-gray-700 mb-2">
            Fecha de Inicio *
          </label>
          <Input
            id="fecha_inicio"
            type="date"
            value={values.fecha_inicio}
            onChange={(e) => handleInputChange('fecha_inicio', e.target.value)}
            error={errors.fecha_inicio}
            required
          />
        </div>

        {/* Fecha fin */}
        <div>
          <label htmlFor="fecha_fin" className="block text-sm font-medium text-gray-700 mb-2">
            Fecha de Fin *
          </label>
          <Input
            id="fecha_fin"
            type="date"
            value={values.fecha_fin}
            onChange={(e) => handleInputChange('fecha_fin', e.target.value)}
            error={errors.fecha_fin}
            required
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
          disabled={loading || loadingInstituciones}
        >
          {submitText}
        </Button>
      </div>
    </form>
  );
}; 