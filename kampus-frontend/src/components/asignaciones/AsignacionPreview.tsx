import React from 'react'
import { Card, CardHeader, CardBody } from '../ui/Card'
import type { AsignacionFormData } from '../../types/asignacion'

interface AsignacionPreviewProps {
  formData: AsignacionFormData
  docente?: { nombre: string; apellido: string }
  asignatura?: { nombre: string; codigo: string }
  grupo?: { nombre: string; grado: { nombre: string } }
  franjaHoraria?: { hora_inicio: string; hora_fin: string }
  anioAcademico?: { nombre: string }
  periodo?: { nombre: string }
}

export const AsignacionPreview: React.FC<AsignacionPreviewProps> = ({
  formData,
  docente,
  asignatura,
  grupo,
  franjaHoraria,
  anioAcademico,
  periodo
}) => {
  if (!formData.docente_id || !formData.asignatura_id || !formData.grupo_id || 
      !formData.franja_horaria_id || !formData.dia_semana || !formData.anio_academico_id) {
    return null
  }

  return (
    <Card className="bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-200">
      <CardHeader>
        <h3 className="text-lg font-semibold text-blue-900 flex items-center">
          <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
          </svg>
          Vista Previa de la Asignación
        </h3>
      </CardHeader>
      <CardBody>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          {/* Información del Docente */}
          <div className="bg-white p-4 rounded-lg border border-blue-200">
            <h4 className="font-medium text-blue-900 mb-2 flex items-center">
              <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              Docente
            </h4>
            <p className="text-gray-700">
              {docente ? `${docente.nombre} ${docente.apellido}` : 'Docente seleccionado'}
            </p>
          </div>

          {/* Información de la Asignatura */}
          <div className="bg-white p-4 rounded-lg border border-blue-200">
            <h4 className="font-medium text-blue-900 mb-2 flex items-center">
              <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
              Asignatura
            </h4>
            <p className="text-gray-700">
              {asignatura ? (asignatura.codigo ? `${asignatura.codigo} - ${asignatura.nombre}` : asignatura.nombre) : 'Asignatura seleccionada'}
            </p>
          </div>

          {/* Información del Grupo */}
          <div className="bg-white p-4 rounded-lg border border-blue-200">
            <h4 className="font-medium text-blue-900 mb-2 flex items-center">
              <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              Grupo
            </h4>
            <p className="text-gray-700">
              {grupo ? `${grupo.nombre} - ${grupo.grado.nombre}` : 'Grupo seleccionado'}
            </p>
          </div>

          {/* Información del Horario */}
          <div className="bg-white p-4 rounded-lg border border-blue-200">
            <h4 className="font-medium text-blue-900 mb-2 flex items-center">
              <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Horario
            </h4>
            <div className="space-y-1">
              <p className="text-gray-700">
                {franjaHoraria ? `${franjaHoraria.hora_inicio} - ${franjaHoraria.hora_fin}` : 'Franja horaria seleccionada'}
              </p>
              <p className="text-sm text-gray-600">
                {formData.dia_semana ? 
                  DIAS_SEMANA.find(dia => dia.value === formData.dia_semana)?.label : 
                  'Día seleccionado'
                }
              </p>
            </div>
          </div>

          {/* Información Temporal */}
          <div className="bg-white p-4 rounded-lg border border-blue-200">
            <h4 className="font-medium text-blue-900 mb-2 flex items-center">
              <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              Año Académico
            </h4>
            <p className="text-gray-700">
              {anioAcademico ? anioAcademico.nombre : 'Año académico seleccionado'}
            </p>
            {periodo && (
              <p className="text-sm text-gray-600 mt-1">
                Período: {periodo.nombre}
              </p>
            )}
          </div>

          {/* Estado */}
          <div className="bg-white p-4 rounded-lg border border-blue-200">
            <h4 className="font-medium text-blue-900 mb-2 flex items-center">
              <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Estado
            </h4>
            <span className={`px-2 py-1 rounded-full text-sm font-medium ${
              formData.estado === 'activo' 
                ? 'bg-green-100 text-green-800' 
                : 'bg-red-100 text-red-800'
            }`}>
              {formData.estado === 'activo' ? 'Activo' : 'Inactivo'}
            </span>
          </div>
        </div>

        {/* Resumen */}
        <div className="mt-6 p-4 bg-blue-100 rounded-lg border border-blue-300">
          <h4 className="font-medium text-blue-900 mb-2">Resumen de la Asignación</h4>
          <p className="text-blue-800 text-sm">
            El docente <strong>{docente ? `${docente.nombre} ${docente.apellido}` : 'seleccionado'}</strong> 
            impartirá la asignatura <strong>{asignatura ? asignatura.nombre : 'seleccionada'}</strong> 
            al grupo <strong>{grupo ? grupo.nombre : 'seleccionado'}</strong> 
            los <strong>{formData.dia_semana ? 
              DIAS_SEMANA.find(dia => dia.value === formData.dia_semana)?.label : 
              'días seleccionados'
            }</strong> 
            de <strong>{franjaHoraria ? `${franjaHoraria.hora_inicio} a ${franjaHoraria.hora_fin}` : 'horario seleccionado'}</strong> 
            durante el <strong>{anioAcademico ? anioAcademico.nombre : 'año académico seleccionado'}</strong>
            {periodo && ` en el período ${periodo.nombre}`}.
          </p>
        </div>
      </CardBody>
    </Card>
  )
}

// Constantes para los días de la semana
const DIAS_SEMANA = [
  { value: 'lunes', label: 'Lunes' },
  { value: 'martes', label: 'Martes' },
  { value: 'miercoles', label: 'Miércoles' },
  { value: 'jueves', label: 'Jueves' },
  { value: 'viernes', label: 'Viernes' },
  { value: 'sabado', label: 'Sábado' }
] 