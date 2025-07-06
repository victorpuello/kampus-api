import React, { useState, useEffect } from 'react'
import { Button } from '../ui/Button'
import { Card, CardHeader, CardBody } from '../ui/Card'
import FormSelect from '../ui/FormSelect'
import { useAlertContext } from '../../contexts/AlertContext'
import { asignacionesApi } from '../../api/asignacionesApi'
import { commonApi } from '../../api/commonApi'
import axiosClient from '../../api/axiosClient'
import { AsignacionPreview } from './AsignacionPreview'
import type { AsignacionFormData, Asignacion } from '../../types/asignacion'
import { DIAS_SEMANA, ESTADOS_ASIGNACION } from '../../types/asignacion'

interface AsignacionFormProps {
  asignacion?: Asignacion
  onSuccess: () => void
  onCancel: () => void
}

export const AsignacionForm: React.FC<AsignacionFormProps> = ({
  asignacion,
  onSuccess,
  onCancel
}) => {
  console.log('=== ASIGNACION FORM INICIADO ===')
  console.log('Props recibidas:', { asignacion, onSuccess: !!onSuccess, onCancel: !!onCancel })
  console.log('Asignación completa:', asignacion)
  
  const { showError, showSuccess } = useAlertContext()
  const [loading, setLoading] = useState(false)
  const [formError, setFormError] = useState<string | null>(null)
  const [formData, setFormData] = useState<AsignacionFormData>({
    docente_id: null,
    asignatura_id: null,
    grupo_id: null,
    franja_horaria_id: null,
    dia_semana: null,
    anio_academico_id: null,
    periodo_id: null,
    estado: 'activo'
  })

  // Debug logs
  console.log('AsignacionForm renderizado:', { asignacion, formData })
  console.log('Estado inicial de formData:', formData)

  // Estados para las opciones de los selects
  const [docentes, setDocentes] = useState<Array<{ id: number; nombre: string; apellido: string }>>([])
  const [asignaturas, setAsignaturas] = useState<Array<{ id: number; nombre: string; codigo: string }>>([])
  const [grupos, setGrupos] = useState<Array<{ id: number; nombre: string; grado: { nombre: string } }>>([])
  const [franjasHorarias, setFranjasHorarias] = useState<Array<{ id: number; hora_inicio: string; hora_fin: string }>>([])
  const [aniosAcademicos, setAniosAcademicos] = useState<Array<{ id: number; nombre: string }>>([])
  const [periodos, setPeriodos] = useState<Array<{ id: number; nombre: string }>>([])
  const [loadingData, setLoadingData] = useState(true)

  // Cargar datos iniciales
  useEffect(() => {
    const loadInitialData = async () => {
      try {
        console.log('Iniciando carga de datos iniciales...')
        setLoadingData(true)
        
        // Cargar todos los datos en paralelo
        const [
          docentesData,
          asignaturasData,
          gruposData,
          franjasData,
          aniosData
        ] = await Promise.all([
          commonApi.getDocentes(),
          commonApi.getAsignaturas(),
          commonApi.getGrupos(),
          commonApi.getFranjasHorarias(),
          commonApi.getAniosAcademicos()
        ])
        
        console.log('Datos cargados:', {
          docentes: docentesData.length,
          asignaturas: asignaturasData.length,
          grupos: gruposData.length,
          franjas: franjasData.length,
          anios: aniosData.length
        })
        
        setDocentes(docentesData)
        setAsignaturas(asignaturasData)
        setGrupos(gruposData)
        setFranjasHorarias(franjasData)
        setAniosAcademicos(aniosData)
        
      } catch (error) {
        console.error('Error cargando datos iniciales:', error)
        showError('Error cargando datos iniciales')
      } finally {
        console.log('Finalizando carga de datos iniciales')
        setLoadingData(false)
      }
    }

    loadInitialData()
  }, [showError])

  // Cargar períodos cuando se selecciona un año académico
  useEffect(() => {
    const loadPeriodos = async () => {
      if (formData.anio_academico_id) {
        try {
          const response = await axiosClient.get(`/anios/${formData.anio_academico_id}/periodos`)
          setPeriodos(response.data.data || [])
        } catch (error) {
          console.error('Error cargando períodos:', error)
          setPeriodos([])
        }
      } else {
        setPeriodos([])
      }
    }

    loadPeriodos()
  }, [formData.anio_academico_id])

  // Cargar datos de la asignación si estamos editando
  useEffect(() => {
    if (asignacion) {
      setFormData({
        docente_id: asignacion.docente.id,
        asignatura_id: asignacion.asignatura.id,
        grupo_id: asignacion.grupo.id,
        franja_horaria_id: asignacion.franja_horaria.id,
        dia_semana: asignacion.dia_semana,
        anio_academico_id: asignacion.anio_academico.id,
        periodo_id: asignacion.periodo?.id || null,
        estado: asignacion.estado
      })
    }
  }, [asignacion])

  const handleInputChange = (field: keyof AsignacionFormData, value: any) => {
    setFormData(prev => ({
      ...prev,
      [field]: value
    }))

    // Si se cambia el año académico, limpiar el período
    if (field === 'anio_academico_id') {
      setFormData(prev => ({
        ...prev,
        [field]: value,
        periodo_id: null
      }))
    }
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setLoading(true)
    setFormError(null) // Limpiar errores previos

    try {
      // Validar que todos los campos requeridos estén completos
      if (!formData.docente_id || !formData.asignatura_id || !formData.grupo_id || 
          !formData.franja_horaria_id || !formData.dia_semana || !formData.anio_academico_id) {
        const errorMsg = 'Por favor completa todos los campos requeridos'
        setFormError(errorMsg)
        showError(errorMsg)
        return
      }

      const data = {
        docente_id: formData.docente_id,
        asignatura_id: formData.asignatura_id,
        grupo_id: formData.grupo_id,
        franja_horaria_id: formData.franja_horaria_id,
        dia_semana: formData.dia_semana,
        anio_academico_id: formData.anio_academico_id,
        periodo_id: formData.periodo_id || undefined,
        estado: formData.estado
      }

      if (asignacion) {
        // Actualizar
        await asignacionesApi.updateAsignacion(asignacion.id, { ...data, id: asignacion.id })
        showSuccess('Asignación actualizada exitosamente')
      } else {
        // Crear
        await asignacionesApi.createAsignacion(data)
        showSuccess('Asignación creada exitosamente')
      }

      onSuccess()
    } catch (error: any) {
      console.error('Error guardando asignación:', error)
      
      let errorMessage = 'Error guardando la asignación'
      
      if (error.response?.data?.conflicto) {
        const conflicto = error.response.data
        errorMessage = `Conflicto de horario: ${conflicto.message}`
      } else if (error.response?.data?.message) {
        errorMessage = error.response.data.message
      } else if (error.message) {
        errorMessage = error.message
      }
      
      // Mostrar error tanto en el formulario como en la alerta global
      setFormError(errorMessage)
      showError(errorMessage)
    } finally {
      setLoading(false)
    }
  }

  // Obtener datos para la vista previa
  const getPreviewData = () => {
    const docente = docentes.find(d => d.id === formData.docente_id)
    const asignatura = asignaturas.find(a => a.id === formData.asignatura_id)
    const grupo = grupos.find(g => g.id === formData.grupo_id)
    const franjaHoraria = franjasHorarias.find(f => f.id === formData.franja_horaria_id)
    const anioAcademico = aniosAcademicos.find(a => a.id === formData.anio_academico_id)
    const periodo = periodos.find(p => p.id === formData.periodo_id)

    return {
      docente,
      asignatura,
      grupo,
      franjaHoraria,
      anioAcademico,
      periodo
    }
  }

  if (loadingData) {
    console.log('Formulario en estado de carga (loadingData = true)')
    return (
      <div className="max-w-4xl mx-auto">
        <Card>
          <CardHeader>
            <div className="animate-pulse">
              <div className="h-8 bg-gray-200 rounded w-1/3"></div>
            </div>
          </CardHeader>
          <CardBody>
            <div className="animate-pulse">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                {[...Array(8)].map((_, i) => (
                  <div key={i} className="h-12 bg-gray-200 rounded"></div>
                ))}
              </div>
            </div>
          </CardBody>
        </Card>
      </div>
    )
  }

  console.log('Formulario renderizando contenido principal (loadingData = false)')
  const previewData = getPreviewData()

  return (
    <div className="max-w-6xl mx-auto space-y-6">
      {/* Formulario */}
      <Card>
        <CardHeader>
          <div className="flex items-center justify-between">
            <div>
              <h2 className="text-2xl font-bold text-gray-900">
                {asignacion ? 'Editar Asignación Académica' : 'Nueva Asignación Académica'}
              </h2>
              <p className="text-gray-600 mt-1">
                {asignacion ? 'Modifica los datos de la asignación académica' : 'Asigna un docente a una asignatura y grupo'}
              </p>
            </div>
            <div className="flex items-center space-x-2">
              <span className={`px-3 py-1 rounded-full text-sm font-medium ${
                formData.estado === 'activo' 
                  ? 'bg-green-100 text-green-800' 
                  : 'bg-red-100 text-red-800'
              }`}>
                {formData.estado === 'activo' ? 'Activo' : 'Inactivo'}
              </span>
            </div>
          </div>
        </CardHeader>

        <CardBody>
          {/* Banner de Error */}
          {formError && (
            <div className="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
              <div className="flex items-center">
                <svg className="w-5 h-5 text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                  <h3 className="text-sm font-medium text-red-800">Error en el formulario</h3>
                  <p className="text-sm text-red-700 mt-1">{formError}</p>
                </div>
                <button
                  onClick={() => setFormError(null)}
                  className="ml-auto p-1 text-red-400 hover:text-red-600"
                >
                  <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
          )}

          <form onSubmit={handleSubmit} className="space-y-8">
            {/* Sección 1: Información del Docente y Asignatura */}
            <div className="bg-blue-50 p-6 rounded-lg">
              <h3 className="text-lg font-semibold text-blue-900 mb-4 flex items-center">
                <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Docente y Asignatura
              </h3>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <FormSelect
                  label="Docente"
                  name="docente_id"
                  required
                  value={formData.docente_id || ''}
                  onChange={(e) => handleInputChange('docente_id', Number(e.target.value))}
                  options={docentes.map((docente) => ({
                    value: docente.id,
                    label: `${docente.nombre} ${docente.apellido}`
                  }))}
                  placeholder="Selecciona un docente"
                />

                <FormSelect
                  label="Asignatura"
                  name="asignatura_id"
                  required
                  value={formData.asignatura_id || ''}
                  onChange={(e) => handleInputChange('asignatura_id', Number(e.target.value))}
                  options={asignaturas.map((asignatura) => ({
                    value: asignatura.id,
                    label: asignatura.codigo ? `${asignatura.codigo} - ${asignatura.nombre}` : asignatura.nombre
                  }))}
                  placeholder="Selecciona una asignatura"
                />
              </div>
            </div>

            {/* Sección 2: Información del Grupo y Horario */}
            <div className="bg-green-50 p-6 rounded-lg">
              <h3 className="text-lg font-semibold text-green-900 mb-4 flex items-center">
                <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Grupo y Horario
              </h3>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <FormSelect
                  label="Grupo"
                  name="grupo_id"
                  required
                  value={formData.grupo_id || ''}
                  onChange={(e) => handleInputChange('grupo_id', Number(e.target.value))}
                  options={grupos.map((grupo) => ({
                    value: grupo.id,
                    label: `${grupo.nombre} - ${grupo.grado.nombre}`
                  }))}
                  placeholder="Selecciona un grupo"
                />

                <FormSelect
                  label="Franja Horaria"
                  name="franja_horaria_id"
                  required
                  value={formData.franja_horaria_id || ''}
                  onChange={(e) => handleInputChange('franja_horaria_id', Number(e.target.value))}
                  options={franjasHorarias.map((franja) => ({
                    value: franja.id,
                    label: `${franja.hora_inicio} - ${franja.hora_fin}`
                  }))}
                  placeholder="Selecciona una franja horaria"
                />
              </div>
            </div>

            {/* Sección 3: Información Temporal */}
            <div className="bg-purple-50 p-6 rounded-lg">
              <h3 className="text-lg font-semibold text-purple-900 mb-4 flex items-center">
                <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Información Temporal
              </h3>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                <FormSelect
                  label="Día de la Semana"
                  name="dia_semana"
                  required
                  value={formData.dia_semana || ''}
                  onChange={(e) => handleInputChange('dia_semana', e.target.value)}
                  options={DIAS_SEMANA.map((dia) => ({
                    value: dia.value,
                    label: dia.label
                  }))}
                  placeholder="Selecciona un día"
                />

                <FormSelect
                  label="Año Académico"
                  name="anio_academico_id"
                  required
                  value={formData.anio_academico_id || ''}
                  onChange={(e) => handleInputChange('anio_academico_id', Number(e.target.value))}
                  options={aniosAcademicos.map((anio) => ({
                    value: anio.id,
                    label: anio.nombre
                  }))}
                  placeholder="Selecciona un año académico"
                />

                <FormSelect
                  label="Período (Opcional)"
                  name="periodo_id"
                  value={formData.periodo_id || ''}
                  onChange={(e) => handleInputChange('periodo_id', e.target.value ? Number(e.target.value) : null)}
                  options={[
                    { value: '', label: 'Sin período específico' },
                    ...periodos.map((periodo) => ({
                      value: periodo.id,
                      label: periodo.nombre
                    }))
                  ]}
                  placeholder="Selecciona un período (opcional)"
                />
              </div>
            </div>

            {/* Sección 4: Estado */}
            <div className="bg-gray-50 p-6 rounded-lg">
              <h3 className="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Estado de la Asignación
              </h3>
              <div className="max-w-xs">
                <FormSelect
                  label="Estado"
                  name="estado"
                  value={formData.estado}
                  onChange={(e) => handleInputChange('estado', e.target.value)}
                  options={ESTADOS_ASIGNACION.map((estado) => ({
                    value: estado.value,
                    label: estado.label
                  }))}
                />
              </div>
            </div>

            {/* Botones de Acción */}
            <div className="flex justify-end space-x-4 pt-6 border-t border-gray-200">
              <Button
                type="button"
                variant="secondary"
                onClick={onCancel}
                disabled={loading}
                className="px-6 py-2"
              >
                Cancelar
              </Button>
              <Button
                type="submit"
                disabled={loading}
                className="px-6 py-2"
              >
                {loading ? (
                  <div className="flex items-center">
                    <svg className="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                      <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Guardando...
                  </div>
                ) : (
                  asignacion ? 'Actualizar Asignación' : 'Crear Asignación'
                )}
              </Button>
            </div>
          </form>
        </CardBody>
      </Card>

      {/* Vista Previa */}
      <AsignacionPreview
        formData={formData}
        docente={previewData.docente}
        asignatura={previewData.asignatura}
        grupo={previewData.grupo}
        franjaHoraria={previewData.franjaHoraria}
        anioAcademico={previewData.anioAcademico}
        periodo={previewData.periodo}
      />
    </div>
  )
} 