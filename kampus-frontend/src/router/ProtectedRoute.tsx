import { Navigate, Outlet } from 'react-router-dom'
import { useAuth } from '../hooks/useAuth'

const ProtectedRoute = () => {
  const { isAuthenticated, token, user } = useAuth()
  
  console.log('🛡️ ProtectedRoute ejecutándose...')
  console.log('   isAuthenticated:', isAuthenticated)
  console.log('   token:', token ? 'Presente' : 'Ausente')
  console.log('   user:', user ? `${user.nombre} ${user.apellido}` : 'Ausente')

  if (!isAuthenticated) {
    console.log('❌ Usuario no autenticado, redirigiendo a login')
    return <Navigate to="/login" replace />
  }

  console.log('✅ Usuario autenticado, mostrando contenido')
  return <Outlet />
}

export default ProtectedRoute 