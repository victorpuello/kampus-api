import { Navigate, Outlet } from 'react-router-dom'
import { useAuth } from '../hooks/useAuth'

const ProtectedRoute = () => {
  const { isAuthenticated } = useAuth()
  
  console.log('ProtectedRoute - isAuthenticated:', isAuthenticated)

  if (!isAuthenticated) {
    console.log('Usuario no autenticado, redirigiendo a login')
    return <Navigate to="/login" replace />
  }

  console.log('Usuario autenticado, mostrando contenido')
  return <Outlet />
}

export default ProtectedRoute 