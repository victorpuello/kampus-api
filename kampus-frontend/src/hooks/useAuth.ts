import { useEffect } from 'react'
import { useAuthStore } from '../store/authStore'

export const useAuth = () => {
  const { token, user, isAuthenticated, login, logout } = useAuthStore()

  // Verificar si el token existe y es válido
  const checkAuth = () => {
    if (!token) {
      return false
    }
    
    // Aquí podrías agregar validación adicional del token si es necesario
    // Por ejemplo, verificar si no ha expirado
    return true
  }

  // Efecto para verificar la autenticación al cargar
  useEffect(() => {
    const isValid = checkAuth()
    if (!isAuthenticated && isValid) {
      // Si hay token pero no está marcado como autenticado, actualizar el estado
      useAuthStore.setState({ isAuthenticated: true })
    } else if (isAuthenticated && !isValid) {
      // Si está marcado como autenticado pero no hay token válido, limpiar
      logout()
    }
  }, [token, isAuthenticated, logout])

  return {
    token,
    user,
    isAuthenticated: isAuthenticated && checkAuth(),
    login,
    logout,
    checkAuth,
  }
} 