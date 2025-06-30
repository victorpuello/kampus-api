import axios from 'axios'
import { useAuthStore } from '../store/authStore'

const axiosClient = axios.create({
  baseURL: 'http://localhost:8000/api/v1',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  withCredentials: true
})

axiosClient.interceptors.request.use(
  (config) => {
    const token = useAuthStore.getState().token
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    console.error('Error en la petición:', error)
    return Promise.reject(error)
  }
)

axiosClient.interceptors.response.use(
  (response) => response,
  async (error) => {
    console.error('Error en la respuesta:', error.response || error)
    
    if (error.response?.status === 401) {
      useAuthStore.getState().logout()
    }
    
    // Mejorar el mensaje de error
    let errorMessage = 'Error en la conexión con el servidor'
    
    if (error.response) {
      // El servidor respondió con un código de estado fuera del rango 2xx
      errorMessage = error.response.data?.message || error.response.data?.error || errorMessage
    } else if (error.request) {
      // La petición fue hecha pero no se recibió respuesta
      errorMessage = 'No se pudo conectar con el servidor'
    }
    
    return Promise.reject(new Error(errorMessage))
  }
)

export default axiosClient 