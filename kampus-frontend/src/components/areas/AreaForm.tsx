import React, { useEffect, useState } from 'react';
import { Input } from '../ui/Input';
import { Button } from '../ui/Button';
import axiosClient from '../../api/axiosClient';
import { useAuthStore } from '../../store/authStore';

export interface AreaFormValues {
  nombre: string;
  descripcion?: string;
  codigo?: string;
  color?: string;
  estado: 'activo' | 'inactivo';
  institucion_id: number;
}

interface AreaFormProps {
  values: AreaFormValues;
  onChange: (values: AreaFormValues) => void;
  onSubmit: (values: AreaFormValues) => void;
  loading?: boolean;
  errors?: Partial<Record<keyof AreaFormValues, string>>;
  submitText?: string;
}

export const AreaForm: React.FC<AreaFormProps> = ({
  values,
  onChange,
  onSubmit,
  loading = false,
  errors = {},
  submitText = 'Guardar',
}) => {
  const { user } = useAuthStore();
  const [instituciones, setInstituciones] = useState<Array<{ id: number; nombre: string }>>([]);
  const [loadingInstituciones, setLoadingInstituciones] = useState(false);

  useEffect(() => {
    const fetchInstituciones = async () => {
      setLoadingInstituciones(true);
      try {
        // Si el usuario tiene una institución específica, solo mostrar esa
        if (user?.institucion) {
          setInstituciones([user.institucion]);
          // Establecer automáticamente la institución del usuario
          if (!values.institucion_id) {
            onChange({
              ...values,
              institucion_id: user.institucion.id,
            });
          }
        } else {
          // Si no tiene institución específica, cargar todas (para administradores)
          const response = await axiosClient.get('/instituciones');
          setInstituciones(response.data.data || []);
        }
      } catch (error) {
        console.error('Error al cargar instituciones:', error);
      } finally {
        setLoadingInstituciones(false);
      }
    };

    fetchInstituciones();
  }, [user, values.institucion_id, onChange, values]);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    onSubmit(values);
  };

  const handleInputChange = (field: keyof AreaFormValues, value: string | number) => {
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
            Nombre del Área *
          </label>
          <Input
            id="nombre"
            type="text"
            value={values.nombre}
            onChange={(e) => handleInputChange('nombre', e.target.value)}
            placeholder="Ej: Matemáticas, Ciencias, Humanidades..."
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
            placeholder="Ej: MAT, CIEN, HUM..."
            error={errors.codigo}
          />
        </div>

        {/* Color */}
        <div>
          <label htmlFor="color" className="block text-sm font-medium text-gray-700 mb-2">
            Color
          </label>
          <div className="flex items-center space-x-3">
            <input
              id="color"
              type="color"
              value={values.color || '#3B82F6'}
              onChange={(e) => handleInputChange('color', e.target.value)}
              className="h-10 w-16 rounded border border-gray-300 cursor-pointer"
            />
            <Input
              type="text"
              value={values.color || '#3B82F6'}
              onChange={(e) => handleInputChange('color', e.target.value)}
              placeholder="#3B82F6"
              error={errors.color}
              className="flex-1"
            />
          </div>
        </div>

        {/* Institución */}
        <div>
          <label htmlFor="institucion_id" className="block text-sm font-medium text-gray-700 mb-2">
            Institución *
          </label>
          {instituciones.length === 1 ? (
            <div className="space-y-2">
              <select
                id="institucion_id"
                value={values.institucion_id || ''}
                onChange={(e) => handleInputChange('institucion_id', parseInt(e.target.value))}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50"
                required
                disabled={true}
              >
                {instituciones.map((institucion) => (
                  <option key={institucion.id} value={institucion.id}>
                    {institucion.nombre}
                  </option>
                ))}
              </select>
              <p className="text-sm text-gray-500">
                Área será creada para tu institución actual
              </p>
            </div>
          ) : (
            <select
              id="institucion_id"
              value={values.institucion_id || ''}
              onChange={(e) => handleInputChange('institucion_id', parseInt(e.target.value))}
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white"
              required
              disabled={loadingInstituciones}
            >
              <option value="">Selecciona una institución</option>
              {instituciones.map((institucion) => (
                <option key={institucion.id} value={institucion.id}>
                  {institucion.nombre}
                </option>
              ))}
            </select>
          )}
          {errors.institucion_id && (
            <p className="mt-1 text-sm text-red-600">{errors.institucion_id}</p>
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

      {/* Descripción */}
      <div>
        <label htmlFor="descripcion" className="block text-sm font-medium text-gray-700 mb-2">
          Descripción
        </label>
        <textarea
          id="descripcion"
          value={values.descripcion || ''}
          onChange={(e) => handleInputChange('descripcion', e.target.value)}
          placeholder="Descripción detallada del área académica..."
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
        >
          {submitText}
        </Button>
      </div>
    </form>
  );
}; 