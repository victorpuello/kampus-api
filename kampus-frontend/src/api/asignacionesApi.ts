import axiosClient from './axiosClient'
import type { 
  Asignacion, 
  CreateAsignacionData, 
  UpdateAsignacionData, 
  AsignacionFilters,
  AsignacionConflict 
} from '../types/asignacion'

export const asignacionesApi = {
  // Obtener lista de asignaciones con filtros
  getAsignaciones: async (filters: AsignacionFilters = {}) => {
    const params = new URLSearchParams()
    
    Object.entries(filters).forEach(([key, value]) => {
      if (value !== undefined && value !== null && value !== '') {
        params.append(key, value.toString())
      }
    })
    
    const response = await axiosClient.get(`/asignaciones?${params.toString()}`)
    return response.data
  },

  // Obtener una asignación específica
  getAsignacion: async (id: number) => {
    const response = await axiosClient.get(`/asignaciones/${id}`)
    return response.data
  },

  // Crear nueva asignación
  createAsignacion: async (data: CreateAsignacionData) => {
    const response = await axiosClient.post('/asignaciones', data)
    return response.data
  },

  // Actualizar asignación
  updateAsignacion: async (id: number, data: UpdateAsignacionData) => {
    const response = await axiosClient.put(`/asignaciones/${id}`, data)
    return response.data
  },

  // Eliminar asignación
  deleteAsignacion: async (id: number) => {
    const response = await axiosClient.delete(`/asignaciones/${id}`)
    return response.data
  },

  // Obtener asignaciones por grupo
  getAsignacionesPorGrupo: async (grupoId: number) => {
    const response = await axiosClient.get(`/asignaciones/grupo/${grupoId}`)
    return response.data
  },

  // Obtener asignaciones por docente
  getAsignacionesPorDocente: async (docenteId: number) => {
    const response = await axiosClient.get(`/asignaciones/docente/${docenteId}`)
    return response.data
  },

  // Obtener conflictos de horarios
  getConflictos: async () => {
    const response = await axiosClient.get('/asignaciones/conflictos')
    return response.data
  },

  // Verificar conflictos antes de crear/actualizar
  verificarConflictos: async (data: CreateAsignacionData | UpdateAsignacionData) => {
    try {
      const response = await axiosClient.post('/asignaciones', data)
      return { success: true, data: response.data }
    } catch (error: any) {
      if (error.response?.status === 422 && error.response?.data?.conflicto) {
        return { 
          success: false, 
          conflicto: error.response.data as AsignacionConflict 
        }
      }
      throw error
    }
  }
} 