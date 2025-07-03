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
  user: User | null
  token: string | null
  isAuthenticated: boolean
  login: (email: string, password: string) => Promise<void>
  logout: () => void
}

export const useAuthStore = create<AuthState>()(
  persist(
    (set, get) => ({
      user: null,
      token: null,
      isAuthenticated: false,

      login: async (email: string, password: string) => {
        console.log('Iniciando login con:', email)
        try {
          // Hacer login y obtener token
          const response = await axiosClient.post('/login', {
            email,
            password,
          })

          console.log('Respuesta del servidor:', response.data)
          const { token, user } = response.data

          set({
            user,
            token,
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
          // Hacer logout para invalidar el token en el servidor
          await axiosClient.post('/logout')
          console.log('Logout exitoso en el backend')
        } catch (error) {
          console.error('Error al cerrar sesión en el backend:', error)
        } finally {
          set({
            user: null,
            token: null,
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