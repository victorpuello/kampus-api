import React from 'react'
import { Button } from '../ui/Button'
import { Badge } from '../ui/Badge'
import { Card } from '../ui/Card'
import type { Asignacion } from '../../types/asignacion'

interface AsignacionesTableProps {
  asignaciones: Asignacion[]
  onEdit: (asignacion: Asignacion) => void
  onDelete: (id: number) => void
  loading?: boolean
}

export const AsignacionesTable: React.FC<AsignacionesTableProps> = ({
  asignaciones,
  onEdit,
  onDelete,
  loading = false
}) => {
  const getEstadoColor = (estado: string) => {
    return estado === 'activo' ? 'success' : 'default'
  }

  const formatTime = (time: string) => {
    return time.substring(0, 5) // Mostrar solo HH:MM
  }

  const getDiaLabel = (dia: string) => {
    const dias: Record<string, string> = {
      lunes: 'Lunes',
      martes: 'Martes',
      miercoles: 'Miércoles',
      jueves: 'Jueves',
      viernes: 'Viernes',
      sabado: 'Sábado'
    }
    return dias[dia] || dia
  }

  if (loading) {
    return (
      <Card>
        <div className="p-6">
          <div className="animate-pulse">
            <div className="h-4 bg-gray-200 rounded w-1/4 mb-4"></div>
            <div className="space-y-3">
              {[...Array(5)].map((_, i) => (
                <div key={i} className="h-12 bg-gray-200 rounded"></div>
              ))}
            </div>
          </div>
        </div>
      </Card>
    )
  }

  if (asignaciones.length === 0) {
    return (
      <Card>
        <div className="p-6 text-center">
          <p className="text-gray-500">No hay asignaciones registradas</p>
        </div>
      </Card>
    )
  }

  return (
    <Card>
      <div className="overflow-x-auto">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Docente
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Asignatura
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Grupo
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Horario
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Año Académico
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Estado
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Acciones
              </th>
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-200">
            {asignaciones.map((asignacion) => (
              <tr key={asignacion.id} className="hover:bg-gray-50">
                <td className="px-6 py-4 whitespace-nowrap">
                  <div className="text-sm font-medium text-gray-900">
                    {asignacion.docente.nombre} {asignacion.docente.apellido}
                  </div>
                  <div className="text-sm text-gray-500">
                    {asignacion.docente.email}
                  </div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <div className="text-sm font-medium text-gray-900">
                    {asignacion.asignatura.nombre}
                  </div>
                  <div className="text-sm text-gray-500">
                    {asignacion.asignatura.codigo} - {asignacion.asignatura.area.nombre}
                  </div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <div className="text-sm font-medium text-gray-900">
                    {asignacion.grupo.nombre}
                  </div>
                  <div className="text-sm text-gray-500">
                    {asignacion.grupo.grado.nombre} - {asignacion.grupo.sede.nombre}
                  </div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <div className="text-sm font-medium text-gray-900">
                    {getDiaLabel(asignacion.dia_semana)}
                  </div>
                  <div className="text-sm text-gray-500">
                    {formatTime(asignacion.franja_horaria.hora_inicio)} - {formatTime(asignacion.franja_horaria.hora_fin)}
                  </div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <div className="text-sm font-medium text-gray-900">
                    {asignacion.anio_academico.nombre}
                  </div>
                  {asignacion.periodo && (
                    <div className="text-sm text-gray-500">
                      {asignacion.periodo.nombre}
                    </div>
                  )}
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <Badge variant={getEstadoColor(asignacion.estado)}>
                    {asignacion.estado === 'activo' ? 'Activo' : 'Inactivo'}
                  </Badge>
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div className="flex space-x-2">
                    <Button
                      size="sm"
                      variant="secondary"
                      onClick={() => {
                        console.log('Botón editar clickeado para asignación:', asignacion.id)
                        console.log('Datos de la asignación:', asignacion)
                        try {
                          onEdit(asignacion)
                        } catch (error) {
                          console.error('Error al editar asignación:', error)
                        }
                      }}
                      className="flex items-center space-x-1"
                    >
                      <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                      <span>Editar</span>
                    </Button>
                    <Button
                      size="sm"
                      variant="danger"
                      onClick={() => {
                        console.log('Botón eliminar clickeado para asignación:', asignacion.id)
                        onDelete(asignacion.id)
                      }}
                      className="flex items-center space-x-1"
                    >
                      <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                      <span>Eliminar</span>
                    </Button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </Card>
  )
} 