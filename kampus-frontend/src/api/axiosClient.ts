import axios from 'axios'
import { useAuthStore } from '../store/authStore'
import { API_CONFIG } from '../config/api'

const axiosClient = axios.create({
  baseURL: API_CONFIG.baseURL,
  timeout: API_CONFIG.timeout,
  headers: API_CONFIG.headers,
  withCredentials: true, // Importante para cookies de sesión
  withXSRFToken: true, // Importante para CSRF protection
})

// Configurar axios para que lea el token CSRF de las cookies
axios.defaults.withCredentials = true
axios.defaults.withXSRFToken = true

// Interceptor para agregar headers necesarios
axiosClient.interceptors.request.use(
  (config) => {
    console.log('🚀 Enviando petición:', config.method?.toUpperCase(), config.url)
    
    // Asegurar que las credenciales se envíen
    config.withCredentials = true
    
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