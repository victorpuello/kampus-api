import { useCallback } from 'react'
import { useAuthStore } from '../store/authStore'

export const useAuth = () => {
  const { token, user, isAuthenticated, login, logout } = useAuthStore()

  // Verificar si el token existe y es válido - memoizada para evitar recreaciones
  const checkAuth = useCallback(() => {
    console.log('🔍 Verificando autenticación...')
    console.log('Token:', token ? 'Presente' : 'Ausente')
    console.log('Usuario:', user ? 'Presente' : 'Ausente')
    console.log('isAuthenticated:', isAuthenticated)
    
    if (!token) {
      console.log('❌ No hay token')
      return false
    }
    
    if (!user) {
      console.log('❌ No hay usuario')
      return false
    }
    
    console.log('✅ Autenticación válida')
    return true
  }, [token, user, isAuthenticated])

  // Estado de autenticación simplificado
  const currentAuthState = isAuthenticated && checkAuth()
  
  console.log('🎯 Estado final de autenticación:', currentAuthState)

  return {
    token,
    user,
    isAuthenticated: currentAuthState,
    login,
    logout,
    checkAuth,
  }
} 