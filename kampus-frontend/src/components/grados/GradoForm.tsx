import React from 'react';
import { Input } from '../ui/Input';
import { Button } from '../ui/Button';
import FormSelect from '../ui/FormSelect';
import { useGradoNiveles } from '../../hooks/useGradoNiveles';

export interface GradoFormValues {
  nombre: string;
  descripcion?: string;
  nivel?: string;
  estado: 'activo' | 'inactivo';
}

interface GradoFormProps {
  values: GradoFormValues;
  onChange: (values: GradoFormValues) => void;
  onSubmit: (values: GradoFormValues) => void;
  loading?: boolean;
  errors?: Partial<Record<keyof GradoFormValues, string>>;
  submitText?: string;
}

export const GradoForm: React.FC<GradoFormProps> = ({
  values,
  onChange,
  onSubmit,
  loading = false,
  errors = {},
  submitText = 'Guardar',
}) => {
  const { niveles, loading: loadingNiveles } = useGradoNiveles();

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    const { name, value } = e.target;
    onChange({ ...values, [name]: value });
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    onSubmit(values);
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-6">
      <Input
        label="Nombre del Grado"
        name="nombre"
        value={values.nombre}
        onChange={handleChange}
        required
        error={errors.nombre}
        placeholder="Ej: Primero, Segundo, Tercero..."
      />
      <Input
        label="Descripción"
        name="descripcion"
        value={values.descripcion || ''}
        onChange={handleChange}
        error={errors.descripcion}
        placeholder="Descripción opcional"
      />
      <FormSelect
        label="Nivel Educativo"
        name="nivel"
        value={values.nivel || ''}
        onChange={handleChange}
        required
        options={niveles}
        placeholder="Selecciona un nivel educativo"
        error={errors.nivel}
        disabled={loadingNiveles}
      />
      <div>
        <label className="block text-sm font-medium text-gray-700 mb-1">Estado</label>
        <select
          name="estado"
          value={values.estado}
          onChange={handleChange}
          className="input"
        >
          <option value="activo">Activo</option>
          <option value="inactivo">Inactivo</option>
        </select>
        {errors.estado && <p className="mt-1 text-sm text-red-600">{errors.estado}</p>}
      </div>
      <Button type="submit" loading={loading} className="w-full">
        {submitText}
      </Button>
    </form>
  );
};

export default GradoForm; 