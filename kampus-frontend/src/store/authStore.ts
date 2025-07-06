import { create } from 'zustand'
import { persist } from 'zustand/middleware'
import axiosClient from '../api/axiosClient'
import { SESSION_CONFIG } from '../config/session'

interface User {
  id: number
  nombre: string
  apellido: string
  email: string
  username: string
  estado: string
  institucion: {
    id: number
    nombre: string
  }
  roles: Array<{
    id: number
    nombre: string
    permissions: Array<{
      id: number
      nombre: string
    }>
  }>
}

interface AuthState {
  user: User | null
  token: string | null
  isAuthenticated: boolean
  lastActivity: number | null
  login: (email: string, password: string) => Promise<void>
  logout: () => void
  updateActivity: () => void
  checkInactivity: () => boolean
}

export const useAuthStore = create<AuthState>()(
  persist(
    (set, get) => ({
      user: null,
      token: null,
      isAuthenticated: false,
      lastActivity: null,

      login: async (email: string, password: string) => {
        console.log('🚀 Iniciando login con:', email)
        try {
          // Hacer login y obtener token
          const response = await axiosClient.post('/login', {
            email,
            password,
          })

          console.log('✅ Respuesta del servidor:', response.data)
          const { token, user } = response.data

          // Actualizar estado
          set({
            user,
            token,
            isAuthenticated: true,
          })
          
          console.log('✅ Estado actualizado:', get())
          console.log('✅ Token guardado:', token ? 'Sí' : 'No')
          console.log('✅ Usuario guardado:', user ? 'Sí' : 'No')
          console.log('✅ isAuthenticated:', get().isAuthenticated)
          
        } catch (error: any) {
          console.error('❌ Error en login:', error.response?.data || error.message)
          throw new Error(error.response?.data?.message || 'Error al iniciar sesión')
        }
      },

      logout: async () => {
        console.log('🚪 Iniciando logout...')
        try {
          // Hacer logout para invalidar el token en el servidor
          await axiosClient.post('/logout')
          console.log('✅ Logout exitoso en el backend')
        } catch (error) {
          console.error('⚠️ Error al cerrar sesión en el backend:', error)
        } finally {
          set({
            user: null,
            token: null,
            isAuthenticated: false,
            lastActivity: null,
          })
          console.log('✅ Estado limpiado')
        }
      },

      updateActivity: () => {
        set({ lastActivity: Date.now() })
      },

      checkInactivity: () => {
        const { lastActivity } = get()
        if (!lastActivity) return false
        
        const timeSinceLastActivity = Date.now() - lastActivity
        return timeSinceLastActivity > SESSION_CONFIG.MAX_INACTIVITY_TIME
      },
    }),
    {
      name: 'auth-storage',
    }
  )
) 