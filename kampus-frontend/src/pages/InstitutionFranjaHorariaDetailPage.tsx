import React, { useState, useEffect } from 'react'
import { useParams, Link } from 'react-router-dom'
import { useAuth } from '../hooks/useAuth'
import { useAlert } from '../hooks/useAlert'
import { franjasHorariasApi } from '../api/franjasHorariasApi'
import { institucionesApi } from '../api/institucionesApi'
import { Button } from '../components/ui/Button'
import { Card } from '../components/ui/Card'
import { Badge } from '../components/ui/Badge'
import { LoadingSpinner } from '../components/ui/LoadingSpinner'
import { ArrowLeftIcon, PencilIcon } from '@heroicons/react/outline'
import type { FranjaHoraria, Institucion } from '../types'

const InstitutionFranjaHorariaDetailPage: React.FC = () => {
  const { institutionId, id } = useParams<{ institutionId: string; id: string }>()
  const { user } = useAuth()
  const { showAlert } = useAlert()
  
  const [franjaHoraria, setFranjaHoraria] = useState<FranjaHoraria | null>(null)
  const [institucion, setInstitucion] = useState<Institucion | null>(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    if (institutionId && id) {
      loadData()
    }
  }, [institutionId, id])

  const loadData = async () => {
    try {
      setLoading(true)
      const [franjaResponse, institucionResponse] = await Promise.all([
        franjasHorariasApi.getFromInstitucion(parseInt(institutionId!), parseInt(id!)),
        institucionesApi.getById(parseInt(institutionId!))
      ])
      
      setFranjaHoraria(franjaResponse.data)
      setInstitucion(institucionResponse.data)
    } catch (error) {
      showAlert('error', 'Error al cargar los datos')
    } finally {
      setLoading(false)
    }
  }

  const formatTime = (time: string | undefined | null) => {
    if (!time) return '--:--'
    return time.substring(0, 5) // Formato HH:MM
  }

  const getEstadoBadge = (estado: string) => {
    const variants = {
      activo: 'success',
      inactivo: 'error',
      pendiente: 'warning'
    } as const
    
    return (
      <Badge variant={variants[estado as keyof typeof variants] || 'default'}>
        {estado}
      </Badge>
    )
  }

  if (loading) {
    return (
      <div className="flex justify-center items-center h-64">
        <LoadingSpinner />
      </div>
    )
  }

  if (!franjaHoraria) {
    return (
      <div className="text-center py-12">
        <p className="text-gray-500 text-lg">Franja horaria no encontrada</p>
      </div>
    )
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

      <div className="flex justify-between items-start">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">
            {franjaHoraria.nombre}
          </h1>
          <p className="text-gray-600">
            {institucion?.nombre} - Detalles de la franja horaria
          </p>
        </div>
        <Link
          to={`/instituciones/${institutionId}/franjas-horarias/${id}/editar`}
          className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700"
        >
          <PencilIcon className="h-4 w-4 mr-2" />
          Editar
        </Link>
      </div>

      {/* Información de la Franja */}
      <Card>
        <div className="p-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h3 className="text-lg font-medium text-gray-900 mb-4">
                Información General
              </h3>
              <dl className="space-y-3">
                <div>
                  <dt className="text-sm font-medium text-gray-500">Nombre</dt>
                  <dd className="text-sm text-gray-900">{franjaHoraria.nombre}</dd>
                </div>
                {franjaHoraria.descripcion && (
                  <div>
                    <dt className="text-sm font-medium text-gray-500">Descripción</dt>
                    <dd className="text-sm text-gray-900">{franjaHoraria.descripcion}</dd>
                  </div>
                )}
                <div>
                  <dt className="text-sm font-medium text-gray-500">Estado</dt>
                  <dd className="text-sm text-gray-900">{getEstadoBadge(franjaHoraria.estado)}</dd>
                </div>
              </dl>
            </div>

            <div>
              <h3 className="text-lg font-medium text-gray-900 mb-4">
                Horario
              </h3>
              <dl className="space-y-3">
                <div>
                  <dt className="text-sm font-medium text-gray-500">Hora de Inicio</dt>
                  <dd className="text-sm text-gray-900">{formatTime(franjaHoraria.hora_inicio)}</dd>
                </div>
                <div>
                  <dt className="text-sm font-medium text-gray-500">Hora de Fin</dt>
                  <dd className="text-sm text-gray-900">{formatTime(franjaHoraria.hora_fin)}</dd>
                </div>
                <div>
                  <dt className="text-sm font-medium text-gray-500">Duración</dt>
                  <dd className="text-sm text-gray-900">{franjaHoraria.duracion_minutos} minutos</dd>
                </div>
              </dl>
            </div>
          </div>
        </div>
      </Card>

      {/* Información de la Institución */}
      <Card>
        <div className="p-6">
          <h3 className="text-lg font-medium text-gray-900 mb-4">
            Institución
          </h3>
          <dl className="space-y-3">
            <div>
              <dt className="text-sm font-medium text-gray-500">Nombre</dt>
              <dd className="text-sm text-gray-900">{institucion?.nombre}</dd>
            </div>
            {institucion?.siglas && (
              <div>
                <dt className="text-sm font-medium text-gray-500">Siglas</dt>
                <dd className="text-sm text-gray-900">{institucion.siglas}</dd>
              </div>
            )}
            {institucion?.slogan && (
              <div>
                <dt className="text-sm font-medium text-gray-500">Slogan</dt>
                <dd className="text-sm text-gray-900">{institucion.slogan}</dd>
              </div>
            )}
          </dl>
        </div>
      </Card>
    </div>
  )
}

export default InstitutionFranjaHorariaDetailPage 