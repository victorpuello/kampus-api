import React from 'react';
import { Input } from '../ui/Input';
import { Button } from '../ui/Button';

export interface PeriodoFormValues {
  nombre: string;
  fecha_inicio: string;
  fecha_fin: string;
  anio_id: number;
}

interface PeriodoFormProps {
  values: PeriodoFormValues;
  onChange: (values: PeriodoFormValues) => void;
  onSubmit: (values: PeriodoFormValues) => void;
  loading?: boolean;
  errors?: Partial<Record<keyof PeriodoFormValues, string>>;
  submitText?: string;
  anioId?: number; // Para cuando se crea desde el detalle de un año
}

export const PeriodoForm: React.FC<PeriodoFormProps> = ({
  values,
  onChange,
  onSubmit,
  loading = false,
  errors = {},
  submitText = 'Guardar',
  anioId,
}) => {
  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    onSubmit(values);
  };

  const handleInputChange = (field: keyof PeriodoFormValues, value: string | number) => {
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
            Nombre del Periodo *
          </label>
          <Input
            id="nombre"
            type="text"
            value={values.nombre}
            onChange={(e) => handleInputChange('nombre', e.target.value)}
            placeholder="Ej: Primer Periodo, Segundo Periodo..."
            error={errors.nombre}
            required
          />
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

        {/* Año Académico (solo si no se proporciona anioId) */}
        {!anioId && (
          <div className="md:col-span-2">
            <label htmlFor="anio_id" className="block text-sm font-medium text-gray-700 mb-2">
              Año Académico *
            </label>
            <select
              id="anio_id"
              value={values.anio_id || ''}
              onChange={(e) => handleInputChange('anio_id', Number(e.target.value))}
              className={`w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 ${
                errors.anio_id ? 'border-red-500' : 'border-gray-300'
              } bg-white`}
              required
            >
              <option value="">Seleccionar año académico</option>
              {/* Las opciones se cargarán dinámicamente desde el componente padre */}
            </select>
            {errors.anio_id && (
              <p className="mt-1 text-sm text-red-600">{errors.anio_id}</p>
            )}
          </div>
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
        >
          {submitText}
        </Button>
      </div>
    </form>
  );
}; 