import axios from 'axios'
import { useAuthStore } from '../store/authStore'
import { API_CONFIG } from '../config/api'

const axiosClient = axios.create({
  baseURL: API_CONFIG.baseURL,
  timeout: API_CONFIG.timeout,
  headers: API_CONFIG.headers,
  withCredentials: API_CONFIG.withCredentials,
})

// Interceptor para agregar token de autenticación
axiosClient.interceptors.request.use(
  (config) => {
    console.log('🚀 Enviando petición:', config.method?.toUpperCase(), config.url)
    
    // Obtener el token del store
    const token = useAuthStore.getState().token
    
    // Agregar el token al header si existe
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
      console.log('🔑 Token agregado a la petición')
    }
    
    return config
  },
  (error) => {
    console.error('❌ Error en la petición:', error)
    return Promise.reject(error)
  }
)

axiosClient.interceptors.response.use(
  (response) => {
    console.log('✅ Respuesta exitosa:', response.status, response.config.url)
    return response
  },
  async (error) => {
    console.error('❌ Error en la respuesta:', error.message)
    console.error('Status:', error.response?.status)
    console.error('Data:', error.response?.data)
    
    // Manejar errores de autenticación
    if (error.response?.status === 401) {
      console.log('🔒 Error de autenticación detectado, limpiando sesión')
      
      // Limpiar el store de autenticación
      useAuthStore.getState().logout()
      
      // Redirigir al login si estamos en el navegador
      if (typeof window !== 'undefined') {
        window.location.href = '/login'
      }
    }
    
    return Promise.reject(error)
  }
)

export default axiosClient 