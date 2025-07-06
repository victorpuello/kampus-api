import { useEffect, useRef } from 'react'
import { useAuthStore } from '../store/authStore'
import axiosClient from '../api/axiosClient'

export const useTokenRefresh = () => {
  const { token, isAuthenticated } = useAuthStore()
  const intervalRef = useRef<number | null>(null)

  useEffect(() => {
    // Solo ejecutar si el usuario está autenticado
    if (!isAuthenticated || !token) {
      if (intervalRef.current) {
        clearInterval(intervalRef.current)
        intervalRef.current = null
      }
      return
    }

    // Función para verificar y renovar el token
    const checkAndRefreshToken = async () => {
      try {
        console.log('🔄 Verificando token...')
        const response = await axiosClient.get('/verify-token')
        
        if (response.data.valid) {
          console.log('✅ Token válido')
          
          // Si se recibió un nuevo token, actualizarlo
          if (response.data.new_token) {
            console.log('🔄 Token renovado automáticamente')
            useAuthStore.setState((state) => ({
              ...state,
              token: response.data.new_token,
            }))
          }
        } else {
          console.log('❌ Token inválido, cerrando sesión')
          useAuthStore.getState().logout()
        }
      } catch (error: any) {
        console.error('❌ Error al verificar token:', error)
        
        // Si es un error 401, cerrar sesión
        if (error.response?.status === 401) {
          console.log('🔒 Token expirado, cerrando sesión')
          useAuthStore.getState().logout()
        }
      }
    }

    // Verificar inmediatamente
    checkAndRefreshToken()

    // Configurar verificación cada 5 minutos
    intervalRef.current = setInterval(checkAndRefreshToken, 5 * 60 * 1000)

    // Cleanup al desmontar
    return () => {
      if (intervalRef.current) {
        clearInterval(intervalRef.current)
        intervalRef.current = null
      }
    }
  }, [isAuthenticated, token])

  return null
} 