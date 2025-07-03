import React, { useState, useEffect } from 'react';
import { Button } from '../ui/Button';
import { Input } from '../ui/Input';
import { Card, CardHeader, CardBody } from '../ui/Card';
import { Alert } from '../ui/Alert';

interface SedeFormData {
  institucion_id: number;
  nombre: string;
  direccion: string;
  telefono: string;
}

interface Institucion {
  id: number;
  nombre: string;
  siglas: string;
}

interface SedeFormProps {
  initialData?: Partial<SedeFormData>;
  instituciones: Institucion[];
  onSubmit: (data: SedeFormData) => Promise<void>;
  isLoading?: boolean;
  submitLabel?: string;
}

export const SedeForm: React.FC<SedeFormProps> = ({
  initialData = {},
  instituciones,
  onSubmit,
  isLoading = false,
  submitLabel = 'Guardar Sede'
}) => {
  const [formData, setFormData] = useState<SedeFormData>({
    institucion_id: 0,
    nombre: '',
    direccion: '',
    telefono: '',
    ...initialData
  });

  const [errors, setErrors] = useState<Record<string, string>>({});

  const handleInputChange = (field: keyof SedeFormData, value: string | number) => {
    setFormData(prev => ({ ...prev, [field]: value }));
    if (errors[field]) {
      setErrors(prev => ({ ...prev, [field]: '' }));
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    // Validaciones básicas
    const newErrors: Record<string, string> = {};
    
    if (!formData.institucion_id) {
      newErrors.institucion_id = 'La institución es requerida';
    }
    
    if (!formData.nombre.trim()) {
      newErrors.nombre = 'El nombre de la sede es requerido';
    }

    if (!formData.direccion.trim()) {
      newErrors.direccion = 'La dirección es requerida';
    }

    if (Object.keys(newErrors).length > 0) {
      setErrors(newErrors);
      return;
    }

    try {
      await onSubmit(formData);
    } catch (error) {
      console.error('Error al guardar la sede:', error);
    }
  };

  return (
    <Card className="w-full max-w-2xl mx-auto">
      <CardHeader>
        <h2 className="text-xl font-semibold text-gray-900">Información de la Sede</h2>
        <p className="text-sm text-gray-600">
          Complete todos los campos requeridos para registrar la sede
        </p>
      </CardHeader>
      <CardBody>
        <form onSubmit={handleSubmit} className="space-y-6">
          <div className="space-y-2">
            <label htmlFor="institucion_id" className="block text-sm font-medium text-gray-700">
              Institución *
            </label>
            <select
              id="institucion_id"
              value={formData.institucion_id}
              onChange={(e) => handleInputChange('institucion_id', parseInt(e.target.value))}
              className="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">Seleccione una institución</option>
              {instituciones.map((institucion) => (
                <option key={institucion.id} value={institucion.id}>
                  {institucion.nombre} ({institucion.siglas})
                </option>
              ))}
            </select>
            {errors.institucion_id && (
              <p className="text-sm text-red-600">{errors.institucion_id}</p>
            )}
          </div>

          <div className="space-y-2">
            <label htmlFor="nombre" className="block text-sm font-medium text-gray-700">
              Nombre de la Sede *
            </label>
            <Input
              id="nombre"
              value={formData.nombre}
              onChange={(e) => handleInputChange('nombre', e.target.value)}
              placeholder="Ej: Sede Principal"
              error={errors.nombre}
            />
          </div>

          <div className="space-y-2">
            <label htmlFor="direccion" className="block text-sm font-medium text-gray-700">
              Dirección *
            </label>
            <textarea
              id="direccion"
              value={formData.direccion}
              onChange={(e) => handleInputChange('direccion', e.target.value)}
              placeholder="Ej: Calle 123 #45-67, Ciudad"
              rows={3}
              className="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
            {errors.direccion && (
              <p className="text-sm text-red-600">{errors.direccion}</p>
            )}
          </div>

          <div className="space-y-2">
            <label htmlFor="telefono" className="block text-sm font-medium text-gray-700">
              Teléfono
            </label>
            <Input
              id="telefono"
              value={formData.telefono}
              onChange={(e) => handleInputChange('telefono', e.target.value)}
              placeholder="Ej: 3001234567"
              maxLength={20}
            />
          </div>

          <div className="flex justify-end space-x-4">
            <Button
              type="submit"
              disabled={isLoading}
              className="min-w-[140px]"
            >
              {isLoading ? 'Guardando...' : submitLabel}
            </Button>
          </div>
        </form>
      </CardBody>
    </Card>
  );
}; 