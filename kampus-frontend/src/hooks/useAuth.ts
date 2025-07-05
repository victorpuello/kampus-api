import { useEffect } from 'react'
import { useAuthStore } from '../store/authStore'

export const useAuth = () => {
  const { token, user, isAuthenticated, login, logout } = useAuthStore()

  // Verificar si el token existe y es válido
  const checkAuth = () => {
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
  }

  // Efecto para verificar la autenticación al cargar
  useEffect(() => {
    console.log('🔄 Efecto de verificación de auth ejecutándose...')
    const isValid = checkAuth()
    
    if (!isAuthenticated && isValid) {
      console.log('🔄 Actualizando estado: no autenticado -> autenticado')
      // Si hay token pero no está marcado como autenticado, actualizar el estado
      useAuthStore.setState({ isAuthenticated: true })
    } else if (isAuthenticated && !isValid) {
      console.log('🔄 Limpiando estado: autenticado -> no autenticado')
      // Si está marcado como autenticado pero no hay token válido, limpiar
      logout()
    }
  }, [token, user, isAuthenticated, logout])

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