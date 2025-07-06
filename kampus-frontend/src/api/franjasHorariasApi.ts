import axiosClient from './axiosClient'
import type { FranjaHoraria } from '../types'

export interface FranjaHorariaFilters {
  page?: number
  per_page?: number
  search?: string
  estado?: string
}

export interface CreateFranjaHorariaData {
  nombre: string
  descripcion?: string
  hora_inicio: string
  hora_fin: string
  duracion_minutos: number
  estado: string
}

export interface UpdateFranjaHorariaData extends Partial<CreateFranjaHorariaData> {}

export const franjasHorariasApi = {
  // Métodos generales (sin anidar)
  getAll: (params?: FranjaHorariaFilters) => 
    axiosClient.get<{ data: FranjaHoraria[]; current_page: number; last_page: number; total: number }>('/franjas-horarias', { params }),
  
  getById: (id: number) => 
    axiosClient.get<FranjaHoraria>(`/franjas-horarias/${id}`),
  
  create: (data: CreateFranjaHorariaData) => 
    axiosClient.post<FranjaHoraria>('/franjas-horarias', data),
  
  update: (id: number, data: UpdateFranjaHorariaData) => 
    axiosClient.put<FranjaHoraria>(`/franjas-horarias/${id}`, data),
  
  delete: (id: number) => 
    axiosClient.delete(`/franjas-horarias/${id}`),

  // Métodos anidados en instituciones
  getByInstitucion: (institutionId: number, params?: FranjaHorariaFilters) => 
    axiosClient.get<{ data: FranjaHoraria[]; current_page: number; last_page: number; total: number }>(`/instituciones/${institutionId}/franjas-horarias`, { params }),
  
  getFromInstitucion: (institutionId: number, id: number) => 
    axiosClient.get<FranjaHoraria>(`/instituciones/${institutionId}/franjas-horarias/${id}`),
  
  createForInstitucion: (institutionId: number, data: CreateFranjaHorariaData) => 
    axiosClient.post<FranjaHoraria>(`/instituciones/${institutionId}/franjas-horarias`, data),
  
  updateFromInstitucion: (institutionId: number, id: number, data: UpdateFranjaHorariaData) => 
    axiosClient.put<FranjaHoraria>(`/instituciones/${institutionId}/franjas-horarias/${id}`, data),
  
  deleteFromInstitucion: (institutionId: number, id: number) => 
    axiosClient.delete(`/instituciones/${institutionId}/franjas-horarias/${id}`)
} 