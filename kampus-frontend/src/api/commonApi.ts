import axiosClient from './axiosClient'

export const commonApi = {
  // Obtener docentes
  getDocentes: async () => {
    const response = await axiosClient.get('/docentes?per_page=100')
    return response.data.data || []
  },

  // Obtener asignaturas
  getAsignaturas: async () => {
    const response = await axiosClient.get('/asignaturas?per_page=100')
    return response.data.data || []
  },

  // Obtener grupos
  getGrupos: async () => {
    const response = await axiosClient.get('/grupos?per_page=100')
    return response.data.data || []
  },

  // Obtener franjas horarias
  getFranjasHorarias: async () => {
    const response = await axiosClient.get('/franjas-horarias?per_page=100')
    return response.data.data || []
  },

  // Obtener años académicos
  getAniosAcademicos: async () => {
    const response = await axiosClient.get('/anios?per_page=100')
    return response.data.data || []
  },

  // Obtener períodos
  getPeriodos: async () => {
    const response = await axiosClient.get('/periodos?per_page=100')
    return response.data.data || []
  },

  // Obtener instituciones
  getInstituciones: async () => {
    const response = await axiosClient.get('/instituciones?per_page=100')
    return response.data.data || []
  }
} 