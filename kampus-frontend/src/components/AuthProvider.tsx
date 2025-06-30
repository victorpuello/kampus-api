import { useEffect, type ReactNode } from 'react'
import { useAuth } from '../hooks/useAuth'

interface AuthProviderProps {
  children: ReactNode
}

export const AuthProvider = ({ children }: AuthProviderProps) => {
  const { checkAuth } = useAuth()

  useEffect(() => {
    // Verificar la autenticación al cargar la aplicación
    console.log('🔍 Verificando autenticación inicial...')
    const isAuthenticated = checkAuth()
    console.log('Estado de autenticación:', isAuthenticated)
  }, [checkAuth])

  return <>{children}</>
} 