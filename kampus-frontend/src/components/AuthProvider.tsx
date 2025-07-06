import { type ReactNode } from 'react'

interface AuthProviderProps {
  children: ReactNode
}

export const AuthProvider = ({ children }: AuthProviderProps) => {
  // Eliminado el useEffect que verificaba la autenticación constantemente
  return <>{children}</>
} 