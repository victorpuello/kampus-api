import { create } from 'zustand'
import { persist } from 'zustand/middleware'
import axiosClient from '../api/axiosClient'

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
  token: string | null
  user: User | null
  isAuthenticated: boolean
  login: (email: string, password: string) => Promise<void>
  logout: () => void
}

export const useAuthStore = create<AuthState>()(
  persist(
    (set, get) => ({
      token: null,
      user: null,
      isAuthenticated: false,

      login: async (email: string, password: string) => {
        console.log('Iniciando login con:', email)
        try {
          const response = await axiosClient.post('/login', {
            email,
            password,
          })

          console.log('Respuesta del servidor:', response.data)
          const { token, user } = response.data

          set({
            token,
            user,
            isAuthenticated: true,
          })
          
          console.log('Estado actualizado:', get())
        } catch (error: any) {
          console.error('Error en login:', error.response?.data || error.message)
          throw new Error(error.response?.data?.message || 'Error al iniciar sesión')
        }
      },

      logout: async () => {
        try {
          await axiosClient.post('/logout')
        } catch (error) {
          console.error('Error al cerrar sesión en el backend:', error)
          // Aunque haya un error en el backend, limpiamos el estado local para evitar inconsistencias
        } finally {
          set({
            token: null,
            user: null,
            isAuthenticated: false,
          })
        }
      },
    }),
    {
      name: 'auth-storage',
    }
  )
)

// Log del estado inicial después de la persistencia
console.log('Estado inicial del store (después de persist):', useAuthStore.getState()) 