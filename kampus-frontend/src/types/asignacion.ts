// Tipos para el módulo de asignaciones académicas

export interface Asignacion {
  id: number
  docente: {
    id: number
    nombre: string
    apellido: string
    email: string
    especialidad?: string
    estado: string
    user?: {
      id: number
      nombre: string
      apellido: string
      email: string
    }
  }
  asignatura: {
    id: number
    nombre: string
    codigo: string
    creditos: number
    estado: string
    area: {
      id: number
      nombre: string
      descripcion?: string
    }
  }
  grupo: {
    id: number
    nombre: string
    descripcion?: string
    estado: string
    grado: {
      id: number
      nombre: string
      nivel: string
    }
    sede: {
      id: number
      nombre: string
      institucion: {
        id: number
        nombre: string
      }
    }
  }
  franja_horaria: {
    id: number
    hora_inicio: string
    hora_fin: string
    descripcion?: string
  }
  dia_semana: 'lunes' | 'martes' | 'miercoles' | 'jueves' | 'viernes' | 'sabado'
  anio_academico: {
    id: number
    nombre: string
    fecha_inicio: string
    fecha_fin: string
    estado: string
  }
  periodo?: {
    id: number
    nombre: string
    fecha_inicio: string
    fecha_fin: string
    estado: string
  }
  estado: 'activo' | 'inactivo'
  created_at: string
  updated_at: string
  
  // Atributos calculados
  nombre_docente: string
  nombre_asignatura: string
  nombre_grupo: string
}

export interface CreateAsignacionData {
  docente_id: number
  asignatura_id: number
  grupo_id: number
  franja_horaria_id: number
  dia_semana: 'lunes' | 'martes' | 'miercoles' | 'jueves' | 'viernes' | 'sabado'
  anio_academico_id: number
  periodo_id?: number
  estado?: 'activo' | 'inactivo'
}

export interface UpdateAsignacionData extends Partial<CreateAsignacionData> {
  id: number
}

export interface AsignacionFilters {
  docente_id?: number
  asignatura_id?: number
  grupo_id?: number
  anio_academico_id?: number
  periodo_id?: number
  estado?: string
  institucion_id?: number
  per_page?: number
}

export interface AsignacionConflict {
  tipo: 'docente' | 'grupo'
  mensaje: string
  asignacion_existente?: Asignacion
}

export interface AsignacionFormData {
  docente_id: number | null
  asignatura_id: number | null
  grupo_id: number | null
  franja_horaria_id: number | null
  dia_semana: 'lunes' | 'martes' | 'miercoles' | 'jueves' | 'viernes' | 'sabado' | null
  anio_academico_id: number | null
  periodo_id: number | null
  estado: 'activo' | 'inactivo'
}

export const DIAS_SEMANA = [
  { value: 'lunes', label: 'Lunes' },
  { value: 'martes', label: 'Martes' },
  { value: 'miercoles', label: 'Miércoles' },
  { value: 'jueves', label: 'Jueves' },
  { value: 'viernes', label: 'Viernes' },
  { value: 'sabado', label: 'Sábado' },
] as const

export const ESTADOS_ASIGNACION = [
  { value: 'activo', label: 'Activo' },
  { value: 'inactivo', label: 'Inactivo' },
] as const 