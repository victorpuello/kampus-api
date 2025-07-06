import React, { useState, useEffect } from 'react'
import { useParams, Link } from 'react-router-dom'
import { useAuth } from '../hooks/useAuth'
import { useAlert } from '../hooks/useAlert'
import { franjasHorariasApi } from '../api/franjasHorariasApi'
import { institucionesApi } from '../api/institucionesApi'
import { Button } from '../components/ui/Button'
import { Card } from '../components/ui/Card'
import { Badge } from '../components/ui/Badge'
import { Input } from '../components/ui/Input'
import { Select } from '../components/ui/Select'
import { Pagination } from '../components/ui/Pagination'
import { LoadingSpinner } from '../components/ui/LoadingSpinner'
import { PlusIcon, SearchIcon, FilterIcon, EyeIcon, PencilIcon, TrashIcon } from '@heroicons/react/outline'
import type { FranjaHoraria, Institucion } from '../types'

const InstitutionFranjasHorariasPage: React.FC = () => {
  const { institutionId } = useParams<{ institutionId: string }>()
  const { user } = useAuth()
  const { showAlert } = useAlert()
  
  const [franjasHorarias, setFranjasHorarias] = useState<FranjaHoraria[]>([])
  const [institucion, setInstitucion] = useState<Institucion | null>(null)
  const [loading, setLoading] = useState(true)
  const [searchTerm, setSearchTerm] = useState('')
  const [filterEstado, setFilterEstado] = useState('')
  const [currentPage, setCurrentPage] = useState(1)
  const [totalPages, setTotalPages] = useState(1)
  const [perPage] = useState(10)

  useEffect(() => {
    if (institutionId) {
      loadInstitucion()
      loadFranjasHorarias()
    }
  }, [institutionId, currentPage, searchTerm, filterEstado])

  const loadInstitucion = async () => {
    try {
      const response = await institucionesApi.getById(parseInt(institutionId!))
      setInstitucion(response.data)
    } catch (error) {
      showAlert('Error al cargar la institución', 'error')
    }
  }

  const loadFranjasHorarias = async () => {
    try {
      setLoading(true)
      const params = {
        page: currentPage,
        per_page: perPage,
        search: searchTerm || undefined,
        estado: filterEstado || undefined
      }
      
      const response = await franjasHorariasApi.getByInstitucion(parseInt(institutionId!), params)
      setFranjasHorarias(response.data.data)
      setTotalPages(response.data.last_page)
    } catch (error) {
      showAlert('Error al cargar las franjas horarias', 'error')
    } finally {
      setLoading(false)
    }
  }

  const handleDelete = async (id: number) => {
    if (window.confirm('¿Estás seguro de que deseas eliminar esta franja horaria?')) {
      try {
        await franjasHorariasApi.deleteFromInstitucion(parseInt(institutionId!), id)
        showAlert('Franja horaria eliminada exitosamente', 'success')
        loadFranjasHorarias()
      } catch (error) {
        showAlert('Error al eliminar la franja horaria', 'error')
      }
    }
  }

  const formatTime = (time: string) => {
    return time.substring(0, 5) // Formato HH:MM
  }

  const getEstadoBadge = (estado: string) => {
    const variants = {
      activo: 'success',
      inactivo: 'danger',
      pendiente: 'warning'
    } as const
    
    return (
      <Badge variant={variants[estado as keyof typeof variants] || 'default'}>
        {estado}
      </Badge>
    )
  }

  if (loading && !institucion) {
    return (
      <div className="flex justify-center items-center h-64">
        <LoadingSpinner />
      </div>
    )
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex justify-between items-center">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">
            Franjas Horarias
          </h1>
          <p className="text-gray-600">
            {institucion?.nombre} - Gestión de horarios académicos
          </p>
        </div>
        <div className="flex space-x-3">
          <Link
            to={`/instituciones/${institutionId}/franjas-horarias/crear`}
            className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            <PlusIcon className="h-4 w-4 mr-2" />
            Nueva Franja
          </Link>
        </div>
      </div>

      {/* Filtros */}
      <Card>
        <div className="p-6">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Buscar
              </label>
              <div className="relative">
                <SearchIcon className="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                <Input
                  type="text"
                  placeholder="Buscar franjas horarias..."
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  className="pl-10"
                />
              </div>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Estado
              </label>
              <Select
                value={filterEstado}
                onChange={(e) => setFilterEstado(e.target.value)}
              >
                <option value="">Todos los estados</option>
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
                <option value="pendiente">Pendiente</option>
              </Select>
            </div>
            <div className="flex items-end">
              <Button
                onClick={loadFranjasHorarias}
                className="w-full"
              >
                <FilterIcon className="h-4 w-4 mr-2" />
                Filtrar
              </Button>
            </div>
          </div>
        </div>
      </Card>

      {/* Tabla */}
      <Card>
        <div className="overflow-x-auto">
          {loading ? (
            <div className="flex justify-center items-center h-64">
              <LoadingSpinner />
            </div>
          ) : franjasHorarias.length === 0 ? (
            <div className="text-center py-12">
              <p className="text-gray-500 text-lg">No se encontraron franjas horarias</p>
              <Link
                to={`/instituciones/${institutionId}/franjas-horarias/crear`}
                className="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200"
              >
                <PlusIcon className="h-4 w-4 mr-2" />
                Crear primera franja horaria
              </Link>
            </div>
          ) : (
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Nombre
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Hora Inicio
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Hora Fin
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Duración
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
                {franjasHorarias.map((franja) => (
                  <tr key={franja.id} className="hover:bg-gray-50">
                    <td className="px-6 py-4 whitespace-nowrap">
                      <div className="text-sm font-medium text-gray-900">
                        {franja.nombre}
                      </div>
                      {franja.descripcion && (
                        <div className="text-sm text-gray-500">
                          {franja.descripcion}
                        </div>
                      )}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {formatTime(franja.hora_inicio)}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {formatTime(franja.hora_fin)}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {franja.duracion_minutos} min
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      {getEstadoBadge(franja.estado)}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <div className="flex space-x-2">
                        <Link
                          to={`/instituciones/${institutionId}/franjas-horarias/${franja.id}`}
                          className="text-blue-600 hover:text-blue-900"
                        >
                          <EyeIcon className="h-4 w-4" />
                        </Link>
                        <Link
                          to={`/instituciones/${institutionId}/franjas-horarias/${franja.id}/editar`}
                          className="text-indigo-600 hover:text-indigo-900"
                        >
                          <PencilIcon className="h-4 w-4" />
                        </Link>
                        <button
                          onClick={() => handleDelete(franja.id)}
                          className="text-red-600 hover:text-red-900"
                        >
                          <TrashIcon className="h-4 w-4" />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          )}
        </div>

        {/* Paginación */}
        {totalPages > 1 && (
          <div className="px-6 py-4 border-t border-gray-200">
            <Pagination
              currentPage={currentPage}
              totalPages={totalPages}
              onPageChange={setCurrentPage}
            />
          </div>
        )}
      </Card>
    </div>
  )
}

export default InstitutionFranjasHorariasPage 