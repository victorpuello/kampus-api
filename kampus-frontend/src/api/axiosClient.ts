import axios from 'axios'
import { useAuthStore } from '../store/authStore'
import { API_CONFIG } from '../config/api'

const axiosClient = axios.create({
  baseURL: API_CONFIG.baseURL,
  timeout: API_CONFIG.timeout,
  headers: API_CONFIG.headers,
})

// Interceptor para agregar el token de autorización
axiosClient.interceptors.request.use(
  (config) => {
    console.log('🚀 Enviando petición:', config.method?.toUpperCase(), config.url)
    
    // Obtener el token del store de autenticación
    const token = useAuthStore.getState().token
    
    // Agregar el token al header de autorización si existe
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
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
    
    // Verificar si hay un nuevo token en los headers
    const newToken = response.headers['x-new-token']
    if (newToken) {
      console.log('🔄 Nuevo token recibido, actualizando store')
      useAuthStore.setState((state) => ({
        ...state,
        token: newToken,
      }))
    }
    
    return response
  },
  async (error) => {
    console.error('❌ Error en la respuesta:', error.message)
    console.error('Status:', error.response?.status)
    console.error('Data:', error.response?.data)
    
    // Manejar errores de autenticación
    if (error.response?.status === 401) {
      console.log('🔒 Error de autenticación detectado, limpiando sesión')
      
      // Limpiar el store de autenticación de forma síncrona
      useAuthStore.setState({
        user: null,
        token: null,
        isAuthenticated: false,
      })
      
      // No usar window.location.href para evitar recargas de página
      // La redirección se manejará por el ProtectedRoute
    }
    
    return Promise.reject(error)
  }
)

export default axiosClient 