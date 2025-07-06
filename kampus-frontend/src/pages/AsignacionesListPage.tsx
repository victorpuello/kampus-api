import React, { useState, useEffect } from 'react'
import { Button } from '../components/ui/Button'
import { Card } from '../components/ui/Card'
import { useAlertContext } from '../contexts/AlertContext'
import { usePermission } from '../hooks/usePermission'
import { asignacionesApi } from '../api/asignacionesApi'
import { AsignacionesTable } from '../components/asignaciones/AsignacionesTable'
import { AsignacionesFilters } from '../components/asignaciones/AsignacionesFilters'
import { AsignacionForm } from '../components/asignaciones/AsignacionForm'
import type { Asignacion, AsignacionFilters } from '../types/asignacion'

const AsignacionesListPage: React.FC = () => {
  const { showSuccess, showError } = useAlertContext()
  
  // Verificar permisos
  const canView = usePermission('ver_asignaciones')
  const canCreate = usePermission('crear_asignaciones')
  const canEdit = usePermission('actualizar_asignaciones')
  const canDelete = usePermission('eliminar_asignaciones')
  
  const [asignaciones, setAsignaciones] = useState<Asignacion[]>([])
  const [loading, setLoading] = useState(true)
  const [showForm, setShowForm] = useState(false)
  const [editingAsignacion, setEditingAsignacion] = useState<Asignacion | null>(null)
  const [filters, setFilters] = useState<AsignacionFilters>({
    per_page: 10
  })
  const [pagination, setPagination] = useState({
    current_page: 1,
    last_page: 1,
    total: 0,
    per_page: 10
  })

  // Cargar asignaciones
  const loadAsignaciones = async () => {
    try {
      setLoading(true)
      const response = await asignacionesApi.getAsignaciones(filters)
      
      if (response.data) {
        setAsignaciones(response.data)
        setPagination({
          current_page: response.current_page || 1,
          last_page: response.last_page || 1,
          total: response.total || 0,
          per_page: response.per_page || 10
        })
      }
    } catch (error: any) {
      console.error('Error cargando asignaciones:', error)
      showError('Error cargando las asignaciones')
    } finally {
      setLoading(false)
    }
  }

  // Cargar asignaciones al montar el componente y cuando cambien los filtros
  useEffect(() => {
    loadAsignaciones()
  }, [filters])

  // Manejar creación/edición exitosa
  const handleFormSuccess = () => {
    setShowForm(false)
    setEditingAsignacion(null)
    loadAsignaciones()
  }

  // Manejar cancelación del formulario
  const handleFormCancel = () => {
    setShowForm(false)
    setEditingAsignacion(null)
  }

  // Manejar edición de asignación
  const handleEdit = (asignacion: Asignacion) => {
    console.log('=== INICIANDO EDICIÓN ===')
    console.log('Editando asignación:', asignacion)
    console.log('Estado actual showForm:', showForm)
    console.log('Estado actual editingAsignacion:', editingAsignacion)
    
    try {
      console.log('1. Estableciendo editingAsignacion...')
      setEditingAsignacion(asignacion)
      console.log('2. Estableciendo showForm a true...')
      setShowForm(true)
      console.log('3. Estados actualizados correctamente')
    } catch (error) {
      console.error('Error al preparar edición:', error)
      showError('Error al preparar la edición de la asignación')
    }
  }

  // Manejar eliminación de asignación
  const handleDelete = async (id: number) => {
    try {
      // Confirmación más detallada
      const confirmacion = window.confirm(
        '¿Estás seguro de que quieres eliminar esta asignación?\n\n' +
        'Esta acción no se puede deshacer y eliminará permanentemente la asignación académica.'
      )
      
      if (!confirmacion) {
        return
      }

      console.log('Eliminando asignación con ID:', id)
      
      // Mostrar indicador de carga
      showSuccess('Eliminando asignación...')
      
      const response = await asignacionesApi.deleteAsignacion(id)
      console.log('Respuesta de eliminación:', response)
      
      showSuccess('Asignación eliminada exitosamente')
      await loadAsignaciones() // Recargar la lista
      
    } catch (error: any) {
      console.error('Error eliminando asignación:', error)
      
      let errorMessage = 'Error eliminando la asignación'
      
      if (error.response?.data?.message) {
        errorMessage = error.response.data.message
      } else if (error.message) {
        errorMessage = error.message
      }
      
      showError(errorMessage)
    }
  }

  // Manejar cambio de filtros
  const handleFiltersChange = (newFilters: AsignacionFilters) => {
    setFilters(newFilters)
  }

  // Manejar limpieza de filtros
  const handleClearFilters = () => {
    setFilters({ per_page: 10 })
  }

  console.log('=== RENDERIZANDO PÁGINA ===')
  console.log('showForm:', showForm)
  console.log('editingAsignacion:', editingAsignacion)
  console.log('loading:', loading)
  console.log('canEdit:', canEdit)
  console.log('canCreate:', canCreate)
  console.log('canDelete:', canDelete)
  
  if (showForm) {
    console.log('=== RENDERIZANDO FORMULARIO ===')
    console.log('Props del formulario:', {
      asignacion: editingAsignacion,
      onSuccess: !!handleFormSuccess,
      onCancel: !!handleFormCancel
    })
    console.log('Asignación completa para editar:', editingAsignacion)
    
    return (
      <div className="container mx-auto px-4 py-8">
        <AsignacionForm
          asignacion={editingAsignacion || undefined}
          onSuccess={handleFormSuccess}
          onCancel={handleFormCancel}
        />
      </div>
    )
  } else {
    console.log('=== NO RENDERIZANDO FORMULARIO ===')
    console.log('showForm es false, renderizando lista')
  }

  return (
    <div className="container mx-auto px-4 py-8">
      {/* Header */}
      <div className="flex justify-between items-center mb-6">
        <div>
          <h1 className="text-3xl font-bold text-gray-900">Asignaciones Académicas</h1>
          <p className="text-gray-600 mt-2">
            Gestiona las asignaciones de docentes a asignaturas y grupos
          </p>
        </div>
        
        {canCreate && (
          <Button
            onClick={() => setShowForm(true)}
            className="flex items-center space-x-2"
          >
            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            <span>Nueva Asignación</span>
          </Button>
        )}
      </div>

      {/* Filtros */}
      <AsignacionesFilters
        filters={filters}
        onFiltersChange={handleFiltersChange}
        onClearFilters={handleClearFilters}
      />

      {/* Estadísticas */}
      <Card className="mb-6">
        <div className="p-6">
          <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div className="text-center">
              <div className="text-2xl font-bold text-blue-600">{pagination.total}</div>
              <div className="text-sm text-gray-600">Total Asignaciones</div>
            </div>
            <div className="text-center">
              <div className="text-2xl font-bold text-green-600">
                {asignaciones.filter(a => a.estado === 'activo').length}
              </div>
              <div className="text-sm text-gray-600">Activas</div>
            </div>
            <div className="text-center">
              <div className="text-2xl font-bold text-orange-600">
                {asignaciones.filter(a => a.estado === 'inactivo').length}
              </div>
              <div className="text-sm text-gray-600">Inactivas</div>
            </div>
            <div className="text-center">
              <div className="text-2xl font-bold text-purple-600">
                {new Set(asignaciones.map(a => a.docente.id)).size}
              </div>
              <div className="text-sm text-gray-600">Docentes Asignados</div>
            </div>
          </div>
        </div>
      </Card>

      {/* Tabla de asignaciones */}
      <AsignacionesTable
        asignaciones={asignaciones}
        onEdit={canEdit ? handleEdit : () => {}}
        onDelete={canDelete ? handleDelete : () => {}}
        loading={loading}
      />

      {/* Paginación */}
      {pagination.last_page > 1 && (
        <Card className="mt-6">
          <div className="p-6">
            <div className="flex justify-between items-center">
              <div className="text-sm text-gray-700">
                Mostrando {((pagination.current_page - 1) * pagination.per_page) + 1} a{' '}
                {Math.min(pagination.current_page * pagination.per_page, pagination.total)} de{' '}
                {pagination.total} resultados
              </div>
              
              <div className="flex space-x-2">
                <Button
                  variant="secondary"
                  disabled={pagination.current_page === 1}
                  onClick={() => setFilters(prev => ({ ...prev, page: pagination.current_page - 1 }))}
                >
                  Anterior
                </Button>
                
                <span className="flex items-center px-3 py-2 text-sm text-gray-700">
                  Página {pagination.current_page} de {pagination.last_page}
                </span>
                
                <Button
                  variant="secondary"
                  disabled={pagination.current_page === pagination.last_page}
                  onClick={() => setFilters(prev => ({ ...prev, page: pagination.current_page + 1 }))}
                >
                  Siguiente
                </Button>
              </div>
            </div>
          </div>
        </Card>
      )}
    </div>
  )
}

export default AsignacionesListPage 