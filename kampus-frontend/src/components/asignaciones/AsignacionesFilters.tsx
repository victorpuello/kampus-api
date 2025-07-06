import React, { useState, useEffect } from 'react'
import { Button } from '../ui/Button'
import { Card } from '../ui/Card'
import FormSelect from '../ui/FormSelect'
import { useAlertContext } from '../../contexts/AlertContext'
import { commonApi } from '../../api/commonApi'
import type { AsignacionFilters } from '../../types/asignacion'

interface AsignacionesFiltersProps {
  filters: AsignacionFilters
  onFiltersChange: (filters: AsignacionFilters) => void
  onClearFilters: () => void
}

export const AsignacionesFilters: React.FC<AsignacionesFiltersProps> = ({
  filters,
  onFiltersChange,
  onClearFilters
}) => {
  const { showError } = useAlertContext()
  const [loading, setLoading] = useState(true)
  
  // Estados para las opciones de los filtros
  const [docentes, setDocentes] = useState<Array<{ id: number; nombre: string; apellido: string }>>([])
  const [asignaturas, setAsignaturas] = useState<Array<{ id: number; nombre: string; codigo: string }>>([])
  const [grupos, setGrupos] = useState<Array<{ id: number; nombre: string; grado: { nombre: string } }>>([])
  const [aniosAcademicos, setAniosAcademicos] = useState<Array<{ id: number; nombre: string }>>([])
  const [periodos, setPeriodos] = useState<Array<{ id: number; nombre: string }>>([])
  const [instituciones, setInstituciones] = useState<Array<{ id: number; nombre: string }>>([])

  // Cargar datos para los filtros
  useEffect(() => {
    const loadFilterData = async () => {
      try {
        setLoading(true)
        
        // Cargar todos los datos en paralelo
        const [
          docentesData,
          asignaturasData,
          gruposData,
          aniosData,
          periodosData,
          institucionesData
        ] = await Promise.all([
          commonApi.getDocentes(),
          commonApi.getAsignaturas(),
          commonApi.getGrupos(),
          commonApi.getAniosAcademicos(),
          commonApi.getPeriodos(),
          commonApi.getInstituciones()
        ])
        
        setDocentes(docentesData)
        setAsignaturas(asignaturasData)
        setGrupos(gruposData)
        setAniosAcademicos(aniosData)
        setPeriodos(periodosData)
        setInstituciones(institucionesData)
        
      } catch (error) {
        console.error('Error cargando datos de filtros:', error)
        showError('Error cargando datos de filtros')
      } finally {
        setLoading(false)
      }
    }

    loadFilterData()
  }, [showError])

  const handleFilterChange = (field: keyof AsignacionFilters, value: any) => {
    onFiltersChange({
      ...filters,
      [field]: value === '' ? undefined : value
    })
  }

  const hasActiveFilters = Object.values(filters).some(value => value !== undefined && value !== '')

  if (loading) {
    return (
      <Card className="mb-6">
        <div className="p-6">
          <div className="animate-pulse">
            <div className="h-6 bg-gray-200 rounded w-1/4 mb-4"></div>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
              {[...Array(8)].map((_, i) => (
                <div key={i} className="h-12 bg-gray-200 rounded"></div>
              ))}
            </div>
          </div>
        </div>
      </Card>
    )
  }

  return (
    <Card className="mb-6">
      <div className="p-6">
        <h3 className="text-lg font-medium text-gray-900 mb-4">Filtros</h3>
        
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
          {/* Filtro por docente */}
          <FormSelect
            label="Docente"
            name="docente_id"
            value={filters.docente_id || ''}
            onChange={(e) => handleFilterChange('docente_id', e.target.value ? Number(e.target.value) : undefined)}
            options={[
              { value: '', label: 'Todos los docentes' },
              ...docentes.map((docente) => ({
                value: docente.id,
                label: `${docente.nombre} ${docente.apellido}`
              }))
            ]}
            placeholder="Selecciona un docente"
          />

          {/* Filtro por asignatura */}
          <FormSelect
            label="Asignatura"
            name="asignatura_id"
            value={filters.asignatura_id || ''}
            onChange={(e) => handleFilterChange('asignatura_id', e.target.value ? Number(e.target.value) : undefined)}
            options={[
              { value: '', label: 'Todas las asignaturas' },
              ...asignaturas.map((asignatura) => ({
                value: asignatura.id,
                label: asignatura.codigo ? `${asignatura.codigo} - ${asignatura.nombre}` : asignatura.nombre
              }))
            ]}
            placeholder="Selecciona una asignatura"
          />

          {/* Filtro por grupo */}
          <FormSelect
            label="Grupo"
            name="grupo_id"
            value={filters.grupo_id || ''}
            onChange={(e) => handleFilterChange('grupo_id', e.target.value ? Number(e.target.value) : undefined)}
            options={[
              { value: '', label: 'Todos los grupos' },
              ...grupos.map((grupo) => ({
                value: grupo.id,
                label: `${grupo.nombre} - ${grupo.grado.nombre}`
              }))
            ]}
            placeholder="Selecciona un grupo"
          />

          {/* Filtro por año académico */}
          <FormSelect
            label="Año Académico"
            name="anio_academico_id"
            value={filters.anio_academico_id || ''}
            onChange={(e) => handleFilterChange('anio_academico_id', e.target.value ? Number(e.target.value) : undefined)}
            options={[
              { value: '', label: 'Todos los años' },
              ...aniosAcademicos.map((anio) => ({
                value: anio.id,
                label: anio.nombre
              }))
            ]}
            placeholder="Selecciona un año"
          />

          {/* Filtro por período */}
          <FormSelect
            label="Período"
            name="periodo_id"
            value={filters.periodo_id || ''}
            onChange={(e) => handleFilterChange('periodo_id', e.target.value ? Number(e.target.value) : undefined)}
            options={[
              { value: '', label: 'Todos los períodos' },
              ...periodos.map((periodo) => ({
                value: periodo.id,
                label: periodo.nombre
              }))
            ]}
            placeholder="Selecciona un período"
          />

          {/* Filtro por estado */}
          <FormSelect
            label="Estado"
            name="estado"
            value={filters.estado || ''}
            onChange={(e) => handleFilterChange('estado', e.target.value || undefined)}
            options={[
              { value: '', label: 'Todos los estados' },
              { value: 'activo', label: 'Activo' },
              { value: 'inactivo', label: 'Inactivo' },
            ]}
            placeholder="Selecciona un estado"
          />

          {/* Filtro por institución */}
          <FormSelect
            label="Institución"
            name="institucion_id"
            value={filters.institucion_id || ''}
            onChange={(e) => handleFilterChange('institucion_id', e.target.value ? Number(e.target.value) : undefined)}
            options={[
              { value: '', label: 'Todas las instituciones' },
              ...instituciones.map((institucion) => ({
                value: institucion.id,
                label: institucion.nombre
              }))
            ]}
            placeholder="Selecciona una institución"
          />

          {/* Filtro por elementos por página */}
          <FormSelect
            label="Elementos por página"
            name="per_page"
            value={filters.per_page || ''}
            onChange={(e) => handleFilterChange('per_page', e.target.value ? Number(e.target.value) : undefined)}
            options={[
              { value: 10, label: '10 elementos' },
              { value: 25, label: '25 elementos' },
              { value: 50, label: '50 elementos' },
              { value: 100, label: '100 elementos' },
            ]}
            placeholder="Selecciona cantidad"
          />
        </div>

        {/* Botones de acción */}
        <div className="flex justify-end space-x-4 mt-6 pt-4 border-t border-gray-200">
          {hasActiveFilters && (
            <Button
              variant="secondary"
              onClick={onClearFilters}
            >
              Limpiar Filtros
            </Button>
          )}
          <Button
            variant="primary"
            onClick={() => {
              // Los filtros se aplican automáticamente al cambiar
              console.log('Filtros aplicados:', filters)
            }}
          >
            Aplicar Filtros
          </Button>
        </div>
      </div>
    </Card>
  )
} 