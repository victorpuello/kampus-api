import { useCallback } from 'react'
import { useNavigate } from 'react-router-dom'
import { useAuthStore } from '../store/authStore'
import { useAlertContext } from '../contexts/AlertContext'

export const useLogout = () => {
  const navigate = useNavigate()
  const { logout } = useAuthStore()
  const { showSuccess, showError } = useAlertContext()

  const handleLogout = useCallback(async () => {
    try {
      console.log('🚪 Iniciando logout con el nuevo microservicio...')
      
      // Usar el nuevo microservicio de logout
      await logout()
      
      console.log('✅ Logout exitoso con el nuevo sistema de tokens')
      showSuccess('Sesión cerrada exitosamente', 'Hasta pronto')
      
      // Redirigir al login
      navigate('/login')
      
    } catch (err: any) {
      console.error('❌ Error en logout:', err)
      const errorMessage = err.message || 'Error al cerrar sesión'
      showError(errorMessage, 'Error de logout')
      
      // Aún así, limpiar el estado local y redirigir
      navigate('/login')
    }
  }, [logout, navigate, showSuccess, showError])

  return { logout: handleLogout }
} 