import React, { useState, useEffect } from 'react'
import { useParams, useNavigate, Link } from 'react-router-dom'
import { useAuth } from '../hooks/useAuth'
import { useAlert } from '../hooks/useAlert'
import { franjasHorariasApi } from '../api/franjasHorariasApi'
import { institucionesApi } from '../api/institucionesApi'
import { Button } from '../components/ui/Button'
import { Card } from '../components/ui/Card'
import { Input } from '../components/ui/Input'
import { Textarea } from '../components/ui/Textarea'
import { Select } from '../components/ui/Select'
import { LoadingSpinner } from '../components/ui/LoadingSpinner'
import { ArrowLeftIcon } from '@heroicons/react/outline'
import type { Institucion } from '../types'

interface FormData {
  nombre: string
  descripcion: string
  hora_inicio: string
  hora_fin: string
  duracion_minutos: number
  estado: string
}

const InstitutionFranjaHorariaCreatePage: React.FC = () => {
  const { institutionId } = useParams<{ institutionId: string }>()
  const navigate = useNavigate()
  const { user } = useAuth()
  const { showAlert } = useAlert()
  
  const [institucion, setInstitucion] = useState<Institucion | null>(null)
  const [loading, setLoading] = useState(false)
  const [formData, setFormData] = useState<FormData>({
    nombre: '',
    descripcion: '',
    hora_inicio: '',
    hora_fin: '',
    duracion_minutos: 45,
    estado: 'activo'
  })

  useEffect(() => {
    if (institutionId) {
      loadInstitucion()
    }
  }, [institutionId])

  const loadInstitucion = async () => {
    try {
      const response = await institucionesApi.getById(parseInt(institutionId!))
      setInstitucion(response.data)
    } catch (error) {
      showAlert('error', 'Error al cargar la institución')
    }
  }

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
    const { name, value } = e.target
    setFormData(prev => ({
      ...prev,
      [name]: value
    }))
  }

  const calculateDuration = () => {
    if (formData.hora_inicio && formData.hora_fin) {
      const start = new Date(`2000-01-01T${formData.hora_inicio}`)
      const end = new Date(`2000-01-01T${formData.hora_fin}`)
      const diffMs = end.getTime() - start.getTime()
      const diffMinutes = Math.round(diffMs / (1000 * 60))
      return diffMinutes > 0 ? diffMinutes : 0
    }
    return 0
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    
    if (!formData.nombre || !formData.hora_inicio || !formData.hora_fin) {
      showAlert('error', 'Por favor completa todos los campos requeridos')
      return
    }

    if (formData.hora_inicio >= formData.hora_fin) {
      showAlert('error', 'La hora de fin debe ser posterior a la hora de inicio')
      return
    }

    try {
      setLoading(true)
      
      const dataToSend = {
        ...formData,
        duracion_minutos: calculateDuration(),
        institucion_id: parseInt(institutionId!)
      }
      
      await franjasHorariasApi.createForInstitucion(parseInt(institutionId!), dataToSend)
      showAlert('success', 'Franja horaria creada exitosamente')
      navigate(`/instituciones/${institutionId}/franjas-horarias`)
    } catch (error: any) {
      const message = error.response?.data?.message || 'Error al crear la franja horaria'
      showAlert('error', message)
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center space-x-4">
        <Link
          to={`/instituciones/${institutionId}/franjas-horarias`}
          className="inline-flex items-center text-sm text-gray-500 hover:text-gray-700"
        >
          <ArrowLeftIcon className="h-4 w-4 mr-1" />
          Volver a franjas horarias
        </Link>
      </div>

      <div>
        <h1 className="text-2xl font-bold text-gray-900">
          Crear Nueva Franja Horaria
        </h1>
        <p className="text-gray-600">
          {institucion?.nombre} - Configurar horario académico
        </p>
      </div>

      {/* Formulario */}
      <Card>
        <form onSubmit={handleSubmit} className="p-6 space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {/* Nombre */}
            <div className="md:col-span-2">
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Nombre de la Franja <span className="text-red-500">*</span>
              </label>
              <Input
                type="text"
                name="nombre"
                value={formData.nombre}
                onChange={handleInputChange}
                placeholder="Ej: Primera hora, Receso, etc."
                required
              />
            </div>

            {/* Descripción */}
            <div className="md:col-span-2">
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Descripción
              </label>
              <Textarea
                name="descripcion"
                value={formData.descripcion}
                onChange={handleInputChange}
                placeholder="Descripción opcional de la franja horaria"
                rows={3}
              />
            </div>

            {/* Hora Inicio */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Hora de Inicio <span className="text-red-500">*</span>
              </label>
              <Input
                type="time"
                name="hora_inicio"
                value={formData.hora_inicio}
                onChange={handleInputChange}
                required
              />
            </div>

            {/* Hora Fin */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Hora de Fin <span className="text-red-500">*</span>
              </label>
              <Input
                type="time"
                name="hora_fin"
                value={formData.hora_fin}
                onChange={handleInputChange}
                required
              />
            </div>

            {/* Duración Calculada */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Duración (calculada automáticamente)
              </label>
              <Input
                type="text"
                value={`${calculateDuration()} minutos`}
                disabled
                className="bg-gray-50"
              />
            </div>

            {/* Estado */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Estado
              </label>
              <Select
                name="estado"
                value={formData.estado}
                onChange={handleInputChange}
              >
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
                <option value="pendiente">Pendiente</option>
              </Select>
            </div>
          </div>

          {/* Botones */}
          <div className="flex justify-end space-x-3 pt-6 border-t border-gray-200">
            <Link
              to={`/instituciones/${institutionId}/franjas-horarias`}
              className="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Cancelar
            </Link>
            <Button
              type="submit"
              disabled={loading}
              className="inline-flex items-center"
            >
              {loading ? (
                <>
                  <LoadingSpinner className="h-4 w-4 mr-2" />
                  Creando...
                </>
              ) : (
                'Crear Franja Horaria'
              )}
            </Button>
          </div>
        </form>
      </Card>
    </div>
  )
}

export default InstitutionFranjaHorariaCreatePage 